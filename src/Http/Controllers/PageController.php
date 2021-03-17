<?php

namespace Rapidez\Core\Http\Controllers;

use Rapidez\Core\Models\Page;
use Rapidez\Core\Models\Product;
use Rapidez\Core\Models\Rewrite;

class PageController
{
    public function show(string $url)
    {
        $page = Page::where('identifier', $url == '/' ? 'home' : $url)->firstOrFail();

        return view('rapidez::page.overview', compact('page'));
    }
}
