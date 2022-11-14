<?php

namespace Rapidez\Core\Http\Controllers\Fallback;

use Illuminate\Http\Request;

class CmsPageController
{
    public function __invoke(Request $request)
    {
        $pageModel = config('rapidez.models.page');
        if ($page = $pageModel::where('identifier', $request->path() == '/' ? 'home' : $request->path())->first()) {
            $pageController = config('rapidez.controllers.page');

            return (new $pageController())->show($page);
        }
    }
}
