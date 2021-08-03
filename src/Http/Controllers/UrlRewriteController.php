<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use TorMorten\Eventy\Facades\Eventy;

class UrlRewriteController
{
    public function __invoke(Request $request)
    {
        $rewriteModel = config('rapidez.models.rewrite');
        $pageModel = config('rapidez.models.page');
        if ($rewrite = $rewriteModel::firstWhere('request_path', $request->path())) {
            if ($rewrite->entity_type == 'category') {
                $categoryController = config('rapidez.controllers.category');

                return (new $categoryController())->show($rewrite->entity_id);
            }

            if ($rewrite->entity_type == 'product') {
                $productController = config('rapidez.controllers.product');

                return (new $productController())->show($rewrite->entity_id);
            }
        }

        if ($page = $pageModel::where('identifier', $request->path() == '/' ? 'home' : $request->path())->first()) {
            $pageController = config('rapidez.controllers.page');

            return (new $pageController())->show($page);
        }

        foreach (Eventy::filter('routes', []) as $route) {
            ob_start();
            require $route;
            if ($output = ob_get_clean()) {
                return $output;
            }
        }

        abort(404);
    }
}
