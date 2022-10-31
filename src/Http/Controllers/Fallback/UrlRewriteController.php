<?php

namespace Rapidez\Core\Http\Controllers\Fallback;

use Illuminate\Http\Request;
use Rapidez\Core\FallbackRoutesRepository;

class UrlRewriteController
{
    public function __invoke(Request $request, FallbackRoutesRepository $routeRepository)
    {
        $rewriteModel = config('rapidez.models.rewrite');
        if (!$rewrite = $rewriteModel::firstWhere('request_path', $request->path())) {
            return;
        }

        if ($rewrite->entity_type == 'category') {
            $categoryController = config('rapidez.controllers.category');

            return (new $categoryController())->show($rewrite->entity_id);
        }

        if ($rewrite->entity_type == 'product') {
            $productController = config('rapidez.controllers.product');

            return (new $productController())->show($rewrite->entity_id);
        }

        if ($rewrite->entity_type == 'custom') {
            return redirect($rewrite->target_path, $rewrite->redirect_type ?: 302);
        }
    }
}
