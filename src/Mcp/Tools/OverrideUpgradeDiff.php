<?php

namespace Rapidez\Core\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Rapidez\Core\Mcp\VendorOverrides;

#[IsReadOnly]
class OverrideUpgradeDiff extends Tool
{
    protected string $name = 'rapidez-override-upgrade-diff';

    protected string $description = 'Get the diffs of one view override after a Composer update: the override\'s own changes (old vendor → override), the vendor changes (old vendor → new vendor), a diff3 merge of the vendor changes into the override and the remaining differences (new vendor → override). Read-only, nothing is written to the project.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'file'       => $schema->string()->description('The override, relative to the project root, as listed by rapidez-changed-overrides.')->required(),
            'from'       => $schema->string()->description('Git ref with the state from before the update, same as given to rapidez-changed-overrides.'),
            'old_vendor' => $schema->string()->description('Absolute path to a vendor folder from before the update, same as given to rapidez-changed-overrides.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $file = $request->get('file');
        $overrides = new VendorOverrides($request->get('from'), $request->get('old_vendor'));

        if (! is_file(base_path($file))) {
            return Response::error("{$file} does not exist");
        }

        $hashes = $overrides->changed()->get($file, []);
        $row = $overrides->resolve($file, $hashes['old'] ?? null, $hashes['new'] ?? null);

        if (! isset($row['diffs'])) {
            return Response::error("No old vendor file for {$file}: {$row['reason']}");
        }

        $verified = match ($row['verified']) {
            true    => 'the old vendor file matches the old vendor-hash',
            false   => 'the old vendor file does NOT match the old vendor-hash, the override may be based on an even older version',
            default => 'there is no old vendor-hash to verify the old vendor file with',
        };

        return Response::text(implode("\n\n", [
            "# {$file}\nVendor: {$row['vendor']} ({$row['package']} {$row['old_version']} → {$row['new_version']}), old file from {$row['old_source']}, {$verified}.",
            "## Override changes (old vendor → override)\nWhat the override changes, the comment describes these.\n\n```diff\n{$row['diffs']['override_changes']}```",
            "## Vendor changes (old vendor → new vendor)\n\n```diff\n{$row['diffs']['vendor_changes']}```",
            "## Merge ({$row['conflicts']} conflicts)\nThe override with the vendor changes applied, conflicts between diff3 markers.\n\n```blade\n{$row['diffs']['merge']}```",
            "## Override vs new vendor (new vendor → override)\n\n```diff\n{$row['diffs']['override_vs_new']}```",
        ]));
    }
}
