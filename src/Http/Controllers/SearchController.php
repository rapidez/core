<?php

namespace Rapidez\Core\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SearchController
{
    public function __invoke(Request $request)
    {
        $query = Str::lower($request->q);
        if ($searchQuery = Cache::remember('search.query.' . Str::slug($query), Carbon::now()->addHour(), function () use ($query) {
            $searchQueryModel = config('rapidez.models.search_query');

            return $searchQueryModel::firstOrCreate(
                ['query_text' => $query],
                ['store_id' => config('rapidez.store')]
            );
        })) {
            $searchQuery->increment('popularity');
            if ($searchQuery->redirect) {
                return redirect($searchQuery->redirect, 301);
            }
        }

        return view('rapidez::search.overview');
    }
}
