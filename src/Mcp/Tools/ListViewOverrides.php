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
#[Name('rapidez-list-view-overrides')]
class ListViewOverrides extends Tool
{
    protected string $description = <<<'DESCRIPTION'
        List the Blade and GraphQL view overrides in resources/views/vendor and the Rapidez theme vendor folders, with the vendor file each one overrides.
        Kinds: "override" (has a vendor counterpart), "include-only" (body is only @includes), "project-only" (no vendor file).
        Per file: has_comment (an "Overwritten to" comment exists), hash (vendor-hash comment: none, match or mismatch with the installed vendor file),
        diff_lines (against the installed vendor file), shadows (the project override a theme file replaces) and notes that need a manual look.
        Use rapidez-view-override-diff for the diff of a single override.
        DESCRIPTION;

    public function __construct(
        protected ViewOverrides $overrides,
    ) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'kind' => $schema->string()
                ->enum(['override', 'include-only', 'project-only'])
                ->description('Only list files of this kind.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $files = $this->overrides->all();

        return Response::json([
            'counts' => $files->countBy('kind')->sortKeys(),
            'files'  => $files->when($request->get('kind'), fn ($files, $kind) => $files->where('kind', $kind))->values(),
        ]);
    }
}
