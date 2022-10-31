<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Rapidez\Core\FallbackRoutesRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class FallbackController
{
    public function __invoke(Request $request, FallbackRoutesRepository $routeRepository)
    {
        foreach ($routeRepository->all() as $route) {
            try {
                $response = App::call($route['action']['uses']);

                // Null response is equal to no response or 404.
                if ($response === null) {
                    abort(404);
                }

                return $response;
            } catch (RouteNotFoundException|NotFoundHttpException $e) {
            }
        }

        abort(404);
    }
}
