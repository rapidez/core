<?php

namespace Rapidez\Core\Http\Controllers\Fallback;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CmsPageController
{
    public function __invoke(Request $request): ?View
    {
        $pageModel = config('rapidez.models.page');
        if ($page = $pageModel::where('identifier', $request->path() == '/' ? 'home' : $request->path())->first()) {
            $pageController = config('rapidez.routing.controllers.page');
            /** @var \Rapidez\Core\Http\Controllers\PageController $pageControllerObject */
            $pageControllerObject = new $pageController;

            return $pageControllerObject->show($page);
        }

        return null;
    }
}
