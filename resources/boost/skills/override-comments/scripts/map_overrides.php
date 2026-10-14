<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

require getcwd() . '/vendor/autoload.php';
$app = require getcwd() . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$out = $argv[1] ?? 'override-map';
File::deleteDirectory("{$out}/diffs");
File::ensureDirectoryExists("{$out}/diffs");

function relative(string $path): ?string
{
    $real = realpath($path);

    return $real ? Str::after($real, realpath(base_path()) . '/') : null;
}

function diff(string $from, string $to): string
{
    return Process::run(['diff', '-u', '-w', $from, $to])->output();
}

function changedLines(string $diff): int
{
    return max(0, preg_match_all('/^[-+]/m', $diff) - 2);
}

function withoutComments(string $content): string
{
    return preg_replace('/\{\{--.*?--\}\}/s', '', $content);
}

function baseVersion(string $override, string $vendor, ?string $hash, string $cache): ?array
{
    [, $owner, $name, $inner] = explode('/', $vendor, 4);
    $log = Process::run(['git', 'log', '--reverse', '--follow', '--format=%H %ad', '--date=short', '--', $override])->output();
    $best = null;

    foreach (array_filter(explode("\n", $log)) as $line) {
        [$commit, $date] = explode(' ', $line);
        $package = collect(json_decode(Process::run(['git', 'show', "{$commit}:composer.lock"])->output(), true))
            ->only(['packages', 'packages-dev'])->flatten(1)->firstWhere('name', "{$owner}/{$name}");

        if (! preg_match('#github\.com/([^/]+/[^/.]+)#', $package['source']['url'] ?? '', $repo)) {
            continue;
        }

        $path = "{$cache}/{$owner}/{$name}/{$package['source']['reference']}/{$inner}";
        if (! file_exists($path)) {
            File::ensureDirectoryExists(dirname($path));
            File::put($path, rescue(fn () => Http::timeout(20)->get("https://raw.githubusercontent.com/{$repo[1]}/{$package['source']['reference']}/{$inner}")->throw()->body(), '', false));
        }

        if (! filesize($path)) {
            continue;
        }

        $base = ['version' => "{$owner}/{$name} {$package['version']}", 'date' => $date, 'path' => $path];

        if ($hash) {
            if (md5_file($path) === $hash) {
                return $base + ['source' => 'hash'];
            }

            continue;
        }

        $score = changedLines(diff($path, $override));
        if (! $best || $score <= $best['score']) {
            $best = $base + ['source' => 'closest', 'score' => $score];
        }
    }

    return $best;
}

$themes = collect([config('rapidez.frontend.theme'), ...data_get(config('rapidez.stores', []), '*.frontend.theme', [])])
    ->filter()->map(fn ($path) => relative($path))->filter()->unique();
$views = collect(config('view.paths'))->map(fn ($path) => relative($path))->filter()->diff($themes);
$viewRoots = $themes->merge($views)->values();

$hints = $app['view']->getFinder()->getHints();
$hints['mail'] = [base_path('vendor/laravel/framework/src/Illuminate/Mail/resources/views')];
$sources = collect($hints)->map(fn ($paths) => collect($paths)
    ->map(fn ($path) => relative($path))
    ->filter()
    ->reject(fn ($path) => Str::startsWith("{$path}/", $viewRoots->map(fn ($root) => "{$root}/")->all()))
    ->values()
);

$columns = ['kind', 'file', 'vendor', 'has_comment', 'hash', 'diff_lines', 'base', 'base_date', 'base_source', 'vendor_changed_since_base', 'shadows'];
$rows = [];

foreach ($viewRoots as $root) {
    if (! is_dir("{$root}/vendor")) {
        continue;
    }

    foreach (File::allFiles("{$root}/vendor") as $file) {
        $file = relative($file->getPathname());
        if (! Str::endsWith($file, ['.blade.php', '.graphql'])) {
            continue;
        }

        $path = Str::after($file, "{$root}/vendor/");
        [$namespace, $view] = explode('/', $path, 2);
        $candidates = $sources->get($namespace, collect())->map(fn ($source) => "{$source}/{$view}")->filter(fn ($candidate) => is_file($candidate))->values();

        $content = file_get_contents($file);
        $hash = preg_match('/vendor-hash:([0-9a-f]+)/', $content, $match) ? $match[1] : null;
        $vendor = $candidates->first(fn ($candidate) => md5_file($candidate) === $hash);
        $hashState = ! $hash ? 'none' : ($vendor ? 'match' : 'mismatch');
        $vendor ??= $candidates->first();

        $lines = collect(explode("\n", withoutComments($content)))->filter(fn ($line) => trim($line) !== '');
        $kind = match (true) {
            ! $vendor                                                                                                         => 'project-only',
            $lines->isNotEmpty() && $lines->every(fn ($line) => preg_match('/^\s*@include(If|When|Unless|First)?\(/', $line)) => 'include-only',
            default                                                                                                           => 'override',
        };

        $row = [
            'kind'        => $kind,
            'file'        => $file,
            'vendor'      => $vendor,
            'has_comment' => preg_match('/overwrit|overrid/i', substr($content, 0, 800)) ? 'yes' : 'no',
            'hash'        => $hashState,
            'shadows'     => $themes->contains($root) ? $views->map(fn ($view) => "{$view}/vendor/{$path}")->first(fn ($candidate) => is_file($candidate)) : null,
        ];

        if ($kind === 'project-only' && $hash) {
            echo "HASH WITHOUT VENDOR FILE (vendor removed it?): {$file}\n";
        }

        if ($kind === 'override') {
            $name = str_replace('/', '__', $file);
            $compare = $vendor;
            $base = $hashState !== 'match' && str_starts_with($vendor, 'vendor/') ? baseVersion($file, $vendor, $hash, "{$out}/cache") : null;

            if ($base && file_get_contents($base['path']) !== file_get_contents($vendor)) {
                $since = diff($base['path'], $vendor);
                File::put("{$out}/diffs/{$name}.vendor-since-base.diff", $since);
                $row['base'] = $base['version'];
                $row['base_date'] = $base['date'];
                $row['base_source'] = $base['source'];
                $row['vendor_changed_since_base'] = changedLines($since);
                $compare = $base['path'];
                echo "FALLEN BEHIND VENDOR: {$file} is based on {$row['base']} ({$row['base_date']}, by {$row['base_source']}); vendor changed {$row['vendor_changed_since_base']} lines since\n";
            } elseif ($hashState === 'mismatch') {
                echo "VENDOR CHANGED SINCE OVERRIDE (hash mismatch, base version not found): {$file}\n";
            }

            $diff = diff($compare, $file);
            File::put("{$out}/diffs/{$name}.diff", $diff);
            $row['diff_lines'] = substr_count($diff, "\n");

            if ($candidates->count() > 1) {
                echo "MULTIPLE VENDOR FILES, compared with {$vendor}: {$file}\n";
            }

            if (preg_replace('/\s+/', '', withoutComments($content)) === preg_replace('/\s+/', '', withoutComments(file_get_contents($vendor)))) {
                echo "IDENTICAL TO VENDOR: {$file}\n";
            }
        }

        $rows[] = collect($columns)->map(fn ($column) => $row[$column] ?? '')->all();
    }
}

File::put("{$out}/map.tsv", collect([$columns, ...$rows])->map(fn ($row) => implode("\t", $row))->implode("\n") . "\n");

collect($rows)->countBy(fn ($row) => $row[0])->sortKeys()->each(fn ($count, $kind) => printf("%4d %s\n", $count, $kind));
