<?php

namespace Rapidez\Core\Http\Controllers\Fallback;

use Illuminate\Http\Request;

class UrlRewriteController
{
    public function __invoke(Request $request)
    {
        $rewriteModel = config('rapidez.models.rewrite');
        if (! $rewrite = $rewriteModel::firstWhere('request_path', $request->path())) {
            return;
        }

        if ($rewrite->redirect_type !== 0) {
            return redirect($rewrite->target_path, $rewrite->redirect_type ?: 302);
        }

        if ($rewrite->entity_type == 'category') {
            $categoryController = config('rapidez.routing.controllers.category');

            return (new $categoryController)->show($rewrite->entity_id);
        }

        if ($rewrite->entity_type == 'product') {
            $productController = config('rapidez.routing.controllers.product');

            return (new $productController)->show($rewrite->entity_id);
        }

        if ($rewrite->entity_type == 'custom') {
            return redirect($rewrite->target_path, $rewrite->redirect_type ?: 302);
        }
    }
}
