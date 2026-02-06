<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use Rapidez\Core\Models\SearchQuery;

class SearchController
{
    public function __invoke(Request $request)
    {
        if (! $request->q) {
            return view('rapidez::search.overview');
        }

        $searchQuery = $this->track($request);

        if ($searchQuery->is_active === 1 && $searchQuery->redirect) {
            return redirect($searchQuery->redirect, 301);
        }

        return view('rapidez::search.overview');
    }

    public function store(Request $request)
    {
        $request->validate(['q' => 'required|string|max:255']);

        if (! $request->q) {
            return response()->json(['success' => true]);
        }

        $this->track($request);

        return response()->json(['success' => true]);
    }

    public function track(Request $request): SearchQuery
    {
        // Prevent automatic indexing each time it is updated.
        config('rapidez.models.search_query')::disableSearchSyncing();
        $searchQuery = config('rapidez.models.search_query')::withoutGlobalScope(IsActiveScope::class)
            ->firstOrCreate(
                [
                    'query_text' => Str::lower($request->q),
                    'store_id'   => config('rapidez.store'),
                ],
                [
                    'num_results' => $request->results ?? 0,
                    'popularity'  => 0,
                ]
            );

        $searchQuery->popularity++;
        if ($request->has('results')) {
            $searchQuery->num_results = $request->results;
        }
        $searchQuery->save();

        return $searchQuery;
    }
}
