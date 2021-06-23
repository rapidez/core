<?php

namespace Rapidez\Core\Http\Controllers;

use Rapidez\Core\Models\Page;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Models\Rewrite;

class PageController
{
    public function show(Page $page)
    {
        return view('rapidez::page.overview', compact('page'));
    }
}
