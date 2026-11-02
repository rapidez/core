<?php

namespace Rapidez\Core\Mcp;

use Composer\InstalledVersions;
use Illuminate\Http\Client\Pool as HttpPool;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Process\Pool as ProcessPool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

/**
 * Maps the Blade and GraphQL view overrides in the "vendor" folder of the
 * view paths and themes to the vendor file they override and, from the
 * git history, to the vendor version an override was originally based on.
 */
class ViewOverrides
{
    /** @var array<string, Collection> Locked packages per commit */
    protected array $locks = [];

    protected ?Collection $themes = null;

    protected ?Collection $viewRoots = null;

    protected ?Collection $sources = null;

    public function all(): Collection
    {
        return $this->viewRoots()
            ->filter(fn ($root) => File::isDirectory(base_path("{$root}/vendor")))
            ->flatMap(fn ($root) => File::allFiles(base_path("{$root}/vendor")))
            ->map(fn ($file) => $this->relative($file->getPathname()))
            ->filter(fn ($file) => Str::endsWith($file, ['.blade.php', '.graphql']))
            ->map(fn ($file) => $this->find($file))
            ->values();
    }

    public function find(string $file): ?array
    {
        $root = $this->viewRoots()->first(fn ($root) => Str::startsWith($file, "{$root}/vendor/"));

        if (! $root || ! File::isFile(base_path($file))) {
            return null;
        }

        $path = Str::after($file, "{$root}/vendor/");
        [$namespace, $view] = explode('/', $path, 2) + [1 => ''];
        $candidates = $this->sources()->get($namespace, collect())
            ->map(fn ($source) => "{$source}/{$view}")
            ->filter(fn ($candidate) => File::isFile($this->absolute($candidate)))
            ->values();

        $content = File::get(base_path($file));
        $hash = preg_match('/vendor-hash:([0-9a-f]+)/', $content, $match) ? $match[1] : null;
        $vendor = $candidates->first(fn ($candidate) => md5_file($this->absolute($candidate)) === $hash);
        $hashState = ! $hash ? 'none' : ($vendor ? 'match' : 'mismatch');
        $vendor ??= $candidates->first();

        $lines = Str::of($this->withoutComments($content))->explode("\n")->filter(fn ($line) => trim($line) !== '');
        $kind = match (true) {
            ! $vendor                                                                                                         => 'project-only',
            $lines->isNotEmpty() && $lines->every(fn ($line) => preg_match('/^\s*@include(If|When|Unless|First)?\(/', $line)) => 'include-only',
            default                                                                                                           => 'override',
        };

        $notes = [];

        if ($kind === 'project-only' && $hash) {
            $notes[] = 'Has a vendor-hash but no vendor file, vendor removed it?';
        }

        if ($kind === 'override' && $candidates->count() > 1) {
            $notes[] = 'Multiple vendor files: ' . $candidates->implode(', ') . ", compared with {$vendor}";
        }

        if ($kind === 'override' && $this->stripped($content) === $this->stripped(File::get($this->absolute($vendor)))) {
            $notes[] = 'Identical to vendor';
        }

        return [
            'kind'        => $kind,
            'file'        => $file,
            'vendor'      => $vendor,
            'has_comment' => (bool) preg_match('/overwrit|overrid/i', substr($content, 0, 800)),
            'hash'        => $hashState,
            'diff_lines'  => $kind === 'override' ? substr_count($this->diff($vendor, $file), "\n") : null,
            'shadows'     => $this->themes()->contains($root)
                ? $this->viewRoots()->diff($this->themes())->map(fn ($view) => "{$view}/vendor/{$path}")->first(fn ($candidate) => File::isFile(base_path($candidate)))
                : null,
            'notes' => $notes,
        ];
    }

    /**
     * Diff an override against the vendor version it was based on, which may
     * be older than the installed vendor file, and diff that base version
     * against the installed vendor file to see what the override misses.
     */
    public function compare(string $file): ?array
    {
        if (! $override = $this->find($file)) {
            return null;
        }

        if ($override['kind'] !== 'override') {
            return $override;
        }

        $vendor = $override['vendor'];
        $hash = preg_match('/vendor-hash:([0-9a-f]+)/', File::get(base_path($file)), $match) ? $match[1] : null;
        $base = $override['hash'] !== 'match' ? $this->baseVersion($file, $vendor, $hash) : null;

        if ($base && File::get($base['path']) !== File::get($this->absolute($vendor))) {
            $since = $this->diff($base['path'], $vendor);

            $override['base'] = $base['version'];
            $override['base_date'] = $base['date'];
            $override['base_source'] = $base['source'];
            $override['vendor_changed_since_base'] = $this->changedLines($since);
            $override['diff'] = $this->diff($base['path'], $file);
            $override['vendor_since_base_diff'] = $since;
            $override['notes'][] = "Fallen behind vendor: based on {$override['base']} ({$base['date']}, by {$base['source']}), vendor changed {$override['vendor_changed_since_base']} lines since";

            return $override;
        }

        if ($override['hash'] === 'mismatch') {
            $override['notes'][] = 'Vendor changed since override (hash mismatch), base version not found';
        }

        $override['diff'] = $this->diff($vendor, $file);

        return $override;
    }

    /**
     * Find the vendor version an override was based on: for every commit that touched
     * the override, fetch the vendor file at the reference locked in composer.lock.
     * With a vendor-hash the matching version wins, otherwise the closest one.
     */
    protected function baseVersion(string $override, string $vendor, ?string $hash): ?array
    {
        if (! [$package, $inner] = $this->package($vendor)) {
            return null;
        }

        $commits = Str::of($this->git(['log', '--reverse', '--follow', '--format=%H %ad', '--date=short', '--', $override]))
            ->explode("\n")
            ->filter()
            ->mapWithKeys(fn ($line) => [Str::before($line, ' ') => Str::after($line, ' ')]);

        $this->loadLocks($commits->keys());

        $versions = $commits
            ->map(fn ($date, $commit) => ($locked = $this->locks[$commit]->get($package)) ? $locked + ['date' => $date] : null)
            ->map(fn ($locked) => $locked ? $locked + ['repo' => Str::match('#github\.com[/:]([^/]+/[^/.]+)#', $locked['url'] ?? '')] : null)
            ->filter(fn ($locked) => $locked && $locked['repo'] && $locked['reference'])
            ->map(fn ($locked) => $locked + [
                'path' => storage_path("framework/cache/rapidez-view-overrides/{$package}/{$locked['reference']}/{$inner}"),
            ])
            ->unique('reference')
            ->values();

        $this->download($versions, $inner);

        $best = null;

        foreach ($versions as $version) {
            if (! File::exists($version['path']) || ! File::size($version['path'])) {
                continue;
            }

            $base = [
                'version' => "{$package} {$version['version']}",
                'date'    => $version['date'],
                'path'    => $version['path'],
            ];

            if ($hash) {
                if (md5_file($version['path']) === $hash) {
                    return $base + ['source' => 'hash'];
                }

                continue;
            }

            $score = $this->changedLines($this->diff($version['path'], $override));
            if (! $best || $score <= $best['score']) {
                $best = $base + ['source' => 'closest', 'score' => $score];
            }
        }

        return $best;
    }

    /**
     * Read the locked packages of all commits at once.
     */
    protected function loadLocks(Collection $commits): void
    {
        $missing = $commits->reject(fn ($commit) => isset($this->locks[$commit]));

        if ($missing->isEmpty()) {
            return;
        }

        $results = Process::pool(fn (ProcessPool $pool) => $missing->each(
            fn ($commit) => $pool->as($commit)->path(base_path())->command(['git', 'show', "{$commit}:composer.lock"])
        ))->start()->wait();

        foreach ($missing as $commit) {
            $this->locks[$commit] = collect(json_decode($results[$commit]->output(), true))
                ->only(['packages', 'packages-dev'])
                ->flatten(1)
                ->mapWithKeys(fn ($package) => [$package['name'] => [
                    'version'   => $package['version'],
                    'reference' => $package['source']['reference'] ?? null,
                    'url'       => $package['source']['url'] ?? null,
                ]]);
        }
    }

    /**
     * Download the vendor file at all locked references at once. Files that didn't
     * exist at a reference are cached empty, other failures are retried next time.
     */
    protected function download(Collection $versions, string $inner): void
    {
        $missing = $versions->reject(fn ($version) => File::exists($version['path']));

        if ($missing->isEmpty()) {
            return;
        }

        $responses = Http::pool(fn (HttpPool $pool) => $missing->map(
            fn ($version) => $pool->as($version['reference'])->timeout(20)->get("https://raw.githubusercontent.com/{$version['repo']}/{$version['reference']}/{$inner}")
        )->all());

        foreach ($missing as $version) {
            $response = $responses[$version['reference']] ?? null;

            if (! $response instanceof HttpResponse || (! $response->successful() && ! $response->notFound())) {
                continue;
            }

            File::ensureDirectoryExists(dirname($version['path']));
            File::put($version['path'], $response->successful() ? $response->body() : '');
        }
    }

    /**
     * The installed Composer package a file belongs to and its path within it.
     *
     * @return array{string, string}|null
     */
    protected function package(string $file): ?array
    {
        $file = realpath($this->absolute($file));

        return collect(InstalledVersions::getInstalledPackages())
            ->mapWithKeys(fn ($package) => [$package => ($path = InstalledVersions::getInstallPath($package)) ? realpath($path) : null])
            ->filter(fn ($path) => $path && Str::startsWith($file, "{$path}/"))
            ->sortByDesc(fn ($path) => strlen($path))
            ->map(fn ($path, $package) => [$package, Str::after($file, "{$path}/")])
            ->first();
    }

    protected function themes(): Collection
    {
        return $this->themes ??= collect([config('rapidez.frontend.theme'), ...data_get(config('rapidez.stores', []), '*.frontend.theme', [])])
            ->filter()
            ->map(fn ($path) => $this->relative($path))
            ->filter()
            ->unique()
            ->values();
    }

    protected function viewRoots(): Collection
    {
        return $this->viewRoots ??= $this->themes()->merge(
            collect(config('view.paths'))->map(fn ($path) => $this->relative($path))->filter()->diff($this->themes())
        )->values();
    }

    /**
     * The paths Laravel resolves per view namespace, plus the mail components.
     */
    protected function sources(): Collection
    {
        if ($this->sources) {
            return $this->sources;
        }

        $hints = View::getFinder()->getHints();
        $hints['mail'] = [base_path('vendor/laravel/framework/src/Illuminate/Mail/resources/views')];

        return $this->sources = collect($hints)->map(fn ($paths) => collect($paths)
            ->map(fn ($path) => $this->relative($path))
            ->filter()
            ->reject(fn ($path) => Str::startsWith("{$path}/", $this->viewRoots()->map(fn ($root) => "{$root}/")->all()))
            ->values()
        );
    }

    /**
     * Relative to the project root when it's within it, absolute otherwise.
     */
    protected function relative(string $path): ?string
    {
        $real = realpath($path);

        return $real ? Str::after($real, realpath(base_path()) . '/') : null;
    }

    protected function absolute(string $path): string
    {
        return Str::startsWith($path, '/') ? $path : base_path($path);
    }

    protected function git(array $arguments): string
    {
        return Process::path(base_path())->run(['git', ...$arguments])->output();
    }

    protected function diff(string $from, string $to): string
    {
        return Process::path(base_path())->run(['diff', '-u', '-w', $from, $to])->output();
    }

    protected function changedLines(string $diff): int
    {
        return max(0, preg_match_all('/^[-+]/m', $diff) - 2);
    }

    protected function withoutComments(string $content): string
    {
        return preg_replace('/\{\{--.*?--\}\}/s', '', $content);
    }

    protected function stripped(string $content): string
    {
        return preg_replace('/\s+/', '', $this->withoutComments($content));
    }
}
