<?php

namespace Rapidez\Core\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Rapidez\Core\Mcp\ViewOverrides;

#[IsReadOnly]
#[Name('rapidez-view-override-diff')]
class ViewOverrideDiff extends Tool
{
    protected string $description = <<<'DESCRIPTION'
        Get the whitespace-insensitive unified diff of a Blade or GraphQL view override against the vendor file it overrides.
        When the override has no vendor-hash, or one that doesn't match the installed vendor file, the vendor version it was based on is looked up:
        for every commit that touched the override, the vendor file is fetched from GitHub at the reference locked in composer.lock at that commit.
        A vendor-hash picks the exact version, otherwise the closest one wins. When found and different from the installed vendor file:
        "diff" is base → override (only the override's own changes), "vendor_since_base_diff" is base → installed vendor (what the override misses),
        and base, base_date, base_source (hash or closest) and vendor_changed_since_base are set. Otherwise "diff" is installed vendor → override.
        DESCRIPTION;

    public function __construct(
        protected ViewOverrides $overrides,
    ) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'file' => $schema->string()
                ->description('The override path relative to the project root, as listed by rapidez-list-view-overrides, e.g. resources/views/vendor/rapidez/product/overview.blade.php')
                ->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        $file = trim((string) $request->string('file'), '/');

        if (! $override = $this->overrides->compare($file)) {
            return Response::error("{$file} is not a view in the vendor folder of a view path or Rapidez theme.");
        }

        return Response::json($override);
    }
}
