<?php

namespace Rapidez\Core\Http\Controllers;

use Rapidez\Core\Models\Page;

class PageController
{
    public function show(Page $page)
    {
        return view('rapidez::page.overview', compact('page'));
    }
}
