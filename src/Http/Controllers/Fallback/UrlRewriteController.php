<?php

namespace Rapidez\Core\Http\Controllers\Fallback;

use Illuminate\Http\Request;

class UrlRewriteController
{
    public function __invoke(Request $request): mixed
    {
        $rewriteModel = config('rapidez.models.rewrite');
        if (! $rewrite = $rewriteModel::firstWhere('request_path', $request->path())) {
            return null;
        }

        if ($rewrite->redirect_type !== 0) {
            return redirect($rewrite->target_path, $rewrite->redirect_type ?: 302);
        }

        if ($rewrite->entity_type == 'category') {
            $categoryController = config('rapidez.routing.controllers.category');
            /** @var \Rapidez\Core\Http\Controllers\CategoryController $categoryControllerObject */
            $categoryControllerObject = new $categoryController;

            return $categoryControllerObject->show($rewrite->entity_id);
        }

        if ($rewrite->entity_type == 'product') {
            $productController = config('rapidez.routing.controllers.product');
            /** @var \Rapidez\Core\Http\Controllers\ProductController $productControllerObject */
            $productControllerObject = new $productController;

            return $productControllerObject->show($rewrite->entity_id);
        }

        return null;
    }
}
