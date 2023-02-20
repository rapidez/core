<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException;
use Illuminate\Support\Facades\App;
use Rapidez\Core\Facades\Rapidez;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class FallbackController
{
    public function __invoke()
    {
        foreach (Rapidez::getAllFallbackRoutes() as $route) {
            try {
                $response = App::call($route['action']['uses']);

                // Null response is equal to no response or 404.
                if ($response === null) {
                    abort(404);
                }

                return $response;
            } catch (RouteNotFoundException|NotFoundHttpException|BackedEnumCaseNotFoundException|ModelNotFoundException|RecordsNotFoundException $e) {
            }
        }

        abort(404);
    }
}
