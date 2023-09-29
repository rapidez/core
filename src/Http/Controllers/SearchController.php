<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SearchController
{
    public function __invoke(Request $request)
    {
        if ($rewrite = Cache::rememberForever('search.query.' . Str::slug($request->q), function () use ($request) {
            $searchQuery = config('rapidez.models.search_query');

            return $searchQuery::where('query_text', $request->q)
                ->where('store_id', config('rapidez.store'))
                ->whereNotNull('redirect')
                ->first();
        })) {
            $rewrite->increment('popularity');

            return redirect($rewrite->redirect);
        }

        return view('rapidez::search.overview');
    }
}
