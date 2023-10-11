<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController
{
    public function __invoke(Request $request)
    {
        $query = Str::lower($request->q);
        $searchQueryModel = config('rapidez.models.search_query');
        $searchQuery = $searchQueryModel::firstOrNew(
            [
                'query_text' => $query,
                'store_id' => config('rapidez.store'),
            ],
            [
                'popularity' => 1,
            ]
        );

        if (!$searchQuery->exists) {
            $searchQuery->save();
            return view('rapidez::search.overview');
        }

        $searchQuery->increment('popularity');
        if ($searchQuery->is_active === 1 && $searchQuery->redirect) {
            return redirect($searchQuery->redirect, 301);
        }

        return view('rapidez::search.overview');
    }
}
