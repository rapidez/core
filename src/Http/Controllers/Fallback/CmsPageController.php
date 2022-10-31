<?php

namespace Rapidez\Core\Http\Controllers\Fallback;

use Illuminate\Http\Request;
use Rapidez\Core\FallbackRoutesRepository;

class CmsPageController
{
    public function __invoke(Request $request, FallbackRoutesRepository $routeRepository)
    {
        $pageModel = config('rapidez.models.page');
        if ($page = $pageModel::where('identifier', $request->path() == '/' ? 'home' : $request->path())->first()) {
            $pageController = config('rapidez.controllers.page');

            return (new $pageController())->show($page);
        }
    }
}
