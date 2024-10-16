<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Contracts\View\View;
use Rapidez\Core\Models\Page;

class PageController
{
    public function show(Page $page): View
    {
        return view('rapidez::page.overview', compact('page'));
    }
}
