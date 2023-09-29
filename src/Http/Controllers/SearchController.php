<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SearchController
{
    public function __invoke(Request $request)
    {
        if ($searchQuery = Cache::rememberForever('search.query.' . Str::slug($request->q), function () use ($request) {
            $searchQueryModel = config('rapidez.models.search_query');

            return $searchQueryModel::where('query_text', $request->q)
                ->where('store_id', config('rapidez.store'))
                ->whereNotNull('redirect')
                ->first();
        })) {
            $searchQuery->increment('popularity');

            return redirect($searchQuery->redirect);
        }

        return view('rapidez::search.overview');
    }
}
