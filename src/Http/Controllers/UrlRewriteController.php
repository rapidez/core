<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Rapidez\Core\Http\Controllers\CategoryController;
use Rapidez\Core\Http\Controllers\PageController;
use Rapidez\Core\Http\Controllers\ProductController;
use Rapidez\Core\Models\Page;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Models\Rewrite;

class UrlRewriteController
{
    public function __invoke(Request $request)
    {
        if ($rewrite = Rewrite::firstWhere('request_path', $request->path())) {
            if ($rewrite->entity_type == 'category') {
                return (new CategoryController)->show($rewrite->entity_id);
            }

            if ($rewrite->entity_type == 'product') {
                return (new ProductController)->show($rewrite->entity_id);
            }
        }

        return (new PageController)->show($request->path());
    }
}
