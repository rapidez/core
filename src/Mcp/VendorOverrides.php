<?php

namespace Rapidez\Core\Mcp;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class VendorOverrides
{
    public ?string $to = null;

    protected array $locks = [];

    protected ?Collection $roots = null;

    protected ?Collection $themes = null;

    protected ?Collection $sources = null;

    protected ?Collection $symlinks = null;

    protected string|false|null $token = null;

    /**
     * Without a from ref the working tree is compared with HEAD when it has
     * vendor-hash changes (staged or not), otherwise HEAD with HEAD~1.
     */
    public function __construct(public ?string $from = null, public ?string $oldVendor = null)
    {
        if (! $this->from) {
            $working = preg_match('/^[-+]\{\{--\s*vendor-hash:/m', $this->git('diff', '-U0', 'HEAD'));
            $this->from = $working ? 'HEAD' : 'HEAD~1';
            $this->to = $working ? null : 'HEAD';
        }
    }

    /**
     * Overrides with a changed vendor-hash, keyed by file with their old and new hash.
     */
    public function changed(): Collection
    {
        $diff = $this->git(...array_filter(['diff', '-U0', '--no-color', '--no-renames', $this->from, $this->to]), ...['--', '*.blade.php']);
        $files = [];
        $file = null;

        foreach (explode("\n", $diff) as $line) {
            if (str_starts_with($line, '+++ ')) {
                $file = $line === '+++ /dev/null' ? null : Str::after($line, '+++ b/');
            } elseif ($file && preg_match('/^([-+])\{\{--\s*vendor-hash:([0-9a-f]+)/', $line, $match)) {
                $files[$file][$match[1] === '-' ? 'old' : 'new'] = $match[2];
            }
        }

        return collect($files)->filter(fn ($hashes) => isset($hashes['new']) && ($hashes['old'] ?? null) !== $hashes['new']);
    }

    /**
     * Packages with a different version or reference between both composer.lock files.
     */
    public function changedPackages(): Collection
    {
        $old = $this->lock($this->from);
        $new = $this->lock($this->to);

        return $old->keys()->merge($new->keys())->unique()->sort()
            ->filter(fn ($name) => data_get($old, "{$name}.source.reference") !== data_get($new, "{$name}.source.reference")
                || data_get($old, "{$name}.version") !== data_get($new, "{$name}.version"))
            ->mapWithKeys(fn ($name) => [$name => [
                'old_version'   => data_get($old, "{$name}.version"),
                'old_reference' => data_get($old, "{$name}.source.reference"),
                'new_version'   => data_get($new, "{$name}.version"),
                'new_reference' => data_get($new, "{$name}.source.reference"),
            ]]);
    }

    /**
     * Overrides without a vendor-hash (like .graphql files) whose vendor file changed in one of the given packages.
     */
    public function unhashed(Collection $packages): Collection
    {
        return $this->roots()
            ->filter(fn ($root) => is_dir($this->path("{$root}/vendor")))
            ->flatMap(fn ($root) => File::allFiles($this->path("{$root}/vendor")))
            ->map(fn ($file) => $this->relative($file->getPathname()))
            ->filter(fn ($file) => Str::endsWith($file, ['.blade.php', '.graphql']) && ! str_contains(File::get($this->path($file)), 'vendor-hash:'))
            ->map(function ($file) use ($packages) {
                if (! ($vendor = $this->candidates($file)->first()) || ! ($package = $this->package($vendor)) || ! $packages->has($package[0])) {
                    return null;
                }

                $old = $this->oldContent($vendor);

                return $old['content'] !== null && $old['content'] === File::get($this->path($vendor)) ? null : $this->resolve($file);
            })
            ->filter()
            ->values();
    }

    /**
     * Everything known about one override, including the diffs between the old vendor, new vendor and override file.
     */
    public function resolve(string $file, ?string $oldHash = null, ?string $newHash = null): array
    {
        $candidates = $this->candidates($file);
        $vendor = $candidates->first(fn ($candidate) => md5_file($this->path($candidate)) === $newHash) ?? $candidates->first();
        $row = ['file' => $file, 'vendor' => $vendor, 'old_hash' => $oldHash, 'new_hash' => $newHash, ...$this->shadows($file)];

        if (! $vendor) {
            return $row + ['old_source' => 'missing', 'reason' => 'No vendor file found for this override'];
        }

        [$name] = $this->package($vendor) ?? [null];
        $old = $this->oldContent($vendor);
        $row += [
            'package'     => $name,
            'old_version' => $name ? data_get($this->lock($this->from), "{$name}.version") : null,
            'new_version' => $name ? data_get($this->lock($this->to), "{$name}.version") : null,
            'old_source'  => $old['source'],
            'reason'      => $old['reason'],
            'verified'    => $oldHash && $old['content'] !== null ? md5($old['content']) === $oldHash : null,
        ];

        if ($old['content'] === null) {
            return $row;
        }

        $override = File::get($this->path($file));
        $new = File::get($this->path($vendor));
        $merge = $this->merge($override, $old['content'], $new);
        $diffs = [
            'override_changes' => $this->diff($old['content'], $override, 'old vendor', 'override'),
            'vendor_changes'   => $this->diff($old['content'], $new, 'old vendor', 'new vendor'),
            'merge'            => $merge['content'],
            'override_vs_new'  => $this->diff($new, $override, 'new vendor', 'override'),
        ];

        return $row + [
            'own_lines'    => $this->changedLines($diffs['override_changes']),
            'vendor_lines' => $this->changedLines($diffs['vendor_changes']),
            'conflicts'    => $merge['conflicts'],
            'diffs'        => $diffs,
        ];
    }

    /**
     * The vendor file before the update, from the old vendor folder or GitHub at the old composer.lock reference.
     */
    public function oldContent(string $vendor): array
    {
        if ($this->oldVendor) {
            $path = rtrim($this->oldVendor, '/') . '/' . Str::after($vendor, 'vendor/');

            return is_file($path)
                ? ['content' => File::get($path), 'source' => 'old-vendor', 'reason' => null]
                : ['content' => null, 'source' => 'missing', 'reason' => "{$path} does not exist"];
        }

        if (! ([$name, $inner] = $this->package($vendor))) {
            return ['content' => null, 'source' => 'missing', 'reason' => 'Vendor file is not in a Composer package'];
        }

        if ($this->lock($this->from)->isEmpty()) {
            return ['content' => null, 'source' => 'missing', 'reason' => "There is no composer.lock at {$this->from}, pass old_vendor"];
        }

        $package = $this->lock($this->from)->get($name);
        if (! $package) {
            return ['content' => null, 'source' => 'missing', 'reason' => "{$name} is not in the old composer.lock"];
        }

        if (! preg_match('#github\.com[/:]([^/]+/[^/]+?)(\.git)?$#', $package['source']['url'] ?? '', $repo)) {
            return ['content' => null, 'source' => 'missing', 'reason' => "{$name} is not installed from GitHub, pass old_vendor"];
        }

        $content = $this->github($repo[1], $package['source']['reference'], $inner);

        return $content === null
            ? ['content' => null, 'source' => 'missing', 'reason' => "Could not fetch {$inner} from {$repo[1]} at {$package['source']['reference']}, pass old_vendor"]
            : ['content' => $content, 'source' => 'github', 'reason' => null];
    }

    protected function github(string $repo, string $reference, string $inner): ?string
    {
        $key = "rapidez-mcp-vendor:{$repo}:{$reference}:{$inner}";
        if (($content = Cache::get($key)) !== null) {
            return $content;
        }

        $content = rescue(fn () => ($this->token()
            ? Http::timeout(20)->withToken($this->token())->accept('application/vnd.github.raw')->get("https://api.github.com/repos/{$repo}/contents/{$inner}", ['ref' => $reference])
            : Http::timeout(20)->get("https://raw.githubusercontent.com/{$repo}/{$reference}/{$inner}")
        )->throw()->body(), null, false);

        if ($content !== null) {
            Cache::forever($key, $content);
        }

        return $content;
    }

    protected function token(): ?string
    {
        if ($this->token === null) {
            $result = Process::path(base_path())->run(['composer', 'config', 'github-oauth.github.com']);
            $this->token = $result->successful() && trim($result->output()) ? trim($result->output()) : false;
        }

        return $this->token ?: null;
    }

    /**
     * The vendor files an override can replace, the one Laravel resolves first comes first.
     */
    public function candidates(string $file): Collection
    {
        $root = $this->roots()->first(fn ($root) => str_starts_with($file, "{$root}/vendor/"));
        if (! $root || ! str_contains($path = Str::after($file, "{$root}/vendor/"), '/')) {
            return collect();
        }

        [$namespace, $view] = explode('/', $path, 2);

        return $this->sources()->get($namespace, collect())
            ->map(fn ($source) => "{$source}/{$view}")
            ->filter(fn ($candidate) => is_file($this->path($candidate)))
            ->values();
    }

    /**
     * Theme overrides replace the project override with the same path on that theme.
     */
    protected function shadows(string $file): array
    {
        $theme = $this->themes()->first(fn ($root) => str_starts_with($file, "{$root}/vendor/"));
        $root = $theme ?? $this->roots()->first(fn ($root) => str_starts_with($file, "{$root}/vendor/"));
        $path = Str::after($file, "{$root}/vendor/");

        return [
            'shadows'     => $theme ? $this->roots()->diff($this->themes())->map(fn ($view) => "{$view}/vendor/{$path}")->first(fn ($candidate) => is_file($this->path($candidate))) : null,
            'shadowed_by' => $theme ? [] : $this->themes()->map(fn ($theme) => "{$theme}/vendor/{$path}")->filter(fn ($candidate) => is_file($this->path($candidate)))->values()->all(),
        ];
    }

    protected function themes(): Collection
    {
        return $this->themes ??= collect([config('rapidez.frontend.theme'), ...data_get(config('rapidez.stores', []), '*.frontend.theme', [])])
            ->filter()->map(fn ($path) => $this->relative($path))->filter(fn ($path) => is_dir($this->path($path)))->unique()->values();
    }

    protected function roots(): Collection
    {
        return $this->roots ??= $this->themes()->merge(collect(config('view.paths'))->map(fn ($path) => $this->relative($path))->diff($this->themes()))->values();
    }

    protected function sources(): Collection
    {
        $hints = app('view')->getFinder()->getHints();
        $hints['mail'] = [base_path('vendor/laravel/framework/src/Illuminate/Mail/resources/views')];

        return $this->sources ??= collect($hints)->map(fn ($paths) => collect($paths)
            ->map(fn ($path) => $this->relative($path))
            ->reject(fn ($path) => Str::startsWith("{$path}/", $this->roots()->map(fn ($root) => "{$root}/")->all()))
            ->values()
        );
    }

    /**
     * Package name and path inside the package, symlinked path repositories stay under vendor/.
     */
    protected function package(string $vendor): ?array
    {
        return preg_match('#^vendor/([^/]+/[^/]+)/(.+)$#', $vendor, $match) ? [$match[1], $match[2]] : null;
    }

    protected function lock(?string $ref): Collection
    {
        return $this->locks[$ref ?? ''] ??= collect(json_decode($ref ? $this->git('show', "{$ref}:composer.lock") : File::get(base_path('composer.lock')), true))
            ->only(['packages', 'packages-dev'])->flatten(1)->keyBy('name');
    }

    protected function git(string ...$arguments): string
    {
        $result = Process::path(base_path())->run(['git', ...$arguments]);

        return $result->successful() ? $result->output() : '';
    }

    public function diff(string $from, string $to, string $fromLabel, string $toLabel): string
    {
        return $this->withFiles([$from, $to], fn ($a, $b) => Process::run(['diff', '-u', '-w', '--label', $fromLabel, '--label', $toLabel, $a, $b])->output());
    }

    public function merge(string $override, string $old, string $new): array
    {
        return $this->withFiles([$override, $old, $new], function ($override, $old, $new) {
            $result = Process::run(['git', 'merge-file', '-p', '--diff3', '-L', 'override', '-L', 'old vendor', '-L', 'new vendor', $override, $old, $new]);

            return ['content' => $result->output(), 'conflicts' => max(0, $result->exitCode())];
        });
    }

    protected function withFiles(array $contents, callable $callback): mixed
    {
        $paths = array_map(function ($content) {
            File::put($path = tempnam(sys_get_temp_dir(), 'rapidez-mcp-'), $content);

            return $path;
        }, $contents);

        try {
            return $callback(...$paths);
        } finally {
            File::delete($paths);
        }
    }

    protected function changedLines(string $diff): int
    {
        return max(0, preg_match_all('/^[-+]/m', $diff) - 2);
    }

    /**
     * Relative to the project without resolving symlinks, so path repositories stay under vendor/.
     */
    protected function relative(string $path): string
    {
        $parts = [];
        foreach (explode('/', str_replace('\\', '/', $path)) as $part) {
            if ($part === '..') {
                array_pop($parts);
            } elseif ($part !== '' && $part !== '.') {
                $parts[] = $part;
            }
        }

        $path = '/' . implode('/', $parts);
        $symlink = $this->symlinks()->keys()->first(fn ($real) => str_starts_with("{$path}/", "{$real}/"));

        return $symlink
            ? $this->symlinks()[$symlink] . Str::after($path, $symlink)
            : Str::after($path, rtrim(base_path(), '/') . '/');
    }

    /**
     * Real paths of symlinked (path repository) packages, mapped to their vendor/ path.
     */
    protected function symlinks(): Collection
    {
        return $this->symlinks ??= $this->lock(null)->keys()
            ->filter(fn ($name) => is_link(base_path("vendor/{$name}")))
            ->mapWithKeys(fn ($name) => [realpath(base_path("vendor/{$name}")) => "vendor/{$name}"]);
    }

    protected function path(string $path): string
    {
        return str_starts_with($path, '/') ? $path : base_path($path);
    }
}
