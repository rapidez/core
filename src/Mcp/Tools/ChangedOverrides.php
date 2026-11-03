<?php

namespace Rapidez\Core\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Arr;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Rapidez\Core\Mcp\VendorOverrides;

#[IsReadOnly]
class ChangedOverrides extends Tool
{
    protected string $name = 'rapidez-changed-overrides';

    protected string $description = 'List the Blade and GraphQL view overrides whose vendor file changed in a Composer update, spotted by changed vendor-hash comments. Compares the working tree with HEAD when vendor-hash lines are changed or staged, otherwise the latest commit with the one before. Returns the changed packages and per override the old and new version, where the old vendor file came from and how many lines the override, vendor and merge conflicts have. Use rapidez-override-upgrade-diff for the diffs of one override.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'from'       => $schema->string()->description('Git ref with the state from before the update, compared with the working tree. Defaults to HEAD when vendor-hash lines are changed, otherwise HEAD~1 compared with HEAD.'),
            'old_vendor' => $schema->string()->description('Absolute path to a vendor folder from before the update, used instead of GitHub for the old vendor files.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $overrides = new VendorOverrides($request->get('from'), $request->get('old_vendor'));
        $packages = $overrides->changedPackages();

        return Response::json([
            'from'      => $overrides->from,
            'to'        => $overrides->to ?? 'working tree',
            'packages'  => $packages,
            'overrides' => $overrides->changed()
                ->map(fn ($hashes, $file) => $overrides->resolve($file, $hashes['old'] ?? null, $hashes['new']))
                ->values()
                ->merge($overrides->unhashed($packages))
                ->map(fn ($row) => Arr::except($row, 'diffs')),
        ]);
    }
}
