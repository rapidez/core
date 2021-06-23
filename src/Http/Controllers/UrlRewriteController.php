<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Rapidez\Core\Models\Page;
use Rapidez\Core\Models\Rewrite;
use TorMorten\Eventy\Facades\Eventy;

class UrlRewriteController
{
    public function __invoke(Request $request)
    {
        if ($rewrite = Rewrite::firstWhere('request_path', $request->path())) {
            if ($rewrite->entity_type == 'category') {
                $categoryController = config('rapidez.controllers.category');
                return (new $categoryController)->show($rewrite->entity_id);
            }

            if ($rewrite->entity_type == 'product') {
                $productController = config('rapidez.controllers.product');
                return (new $productController)->show($rewrite->entity_id);
            }
        }

        if ($page = Page::where('identifier', $request->path() == '/' ? 'home' : $request->path())->first()) {
            $pageController = config('rapidez.controllers.page');
            return (new $pageController)->show($page);
        }

        foreach (Eventy::filter('routes', []) as $route) {
            if ($output = require $route) {
                return $output;
            }
        }

        abort(404);
    }
}
