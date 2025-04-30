<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Traits\Searchable;

class IndexController
{
    public function store(string $model, Request $request)
    {
        $request->validate([
            'ids'   => 'array|required',
            'ids.*' => 'integer',
        ]);

        abort_if(! ($model = $this->resolveModel($model)), 404);

        $passedStores = $request->input('stores');
        if (! $passedStores) {
            return $this->updateSearchable($model, $request->input('ids'));
        }

        $passedStores = Arr::wrap($passedStores);

        if ($passedStores[0] === 'all' || $passedStores[0] === '*') {
            $stores = Rapidez::getStores();
        } else {
            $stores = Rapidez::getStores($passedStores);
        }

        foreach ($stores as $store) {
            Rapidez::setStore($store);

            $this->updateSearchable($model, $request->input('ids'));
        }
    }

    public function destroy(string $model, Request $request)
    {
        $request->validate([
            'ids'   => 'array|required',
            'ids.*' => 'integer',
        ]);

        abort_if(! ($model = $this->resolveModel($model)), 404);

        $passedStores = $request->input('stores');
        if (! $passedStores) {
            return $this->deleteSearchable($model, $request->input('ids'));
        }

        $passedStores = Arr::wrap($passedStores);

        if ($passedStores[0] === 'all' || $passedStores[0] === '*') {
            $stores = Rapidez::getStores();
        } else {
            $stores = Rapidez::getStores($passedStores);
        }

        foreach ($stores as $store) {
            Rapidez::setStore($store);

            $this->deleteSearchable($model, $request->input('ids'));
        }
    }

    protected function deleteSearchable(string $model, array $ids): void
    {
        if (empty($ids)) {
            $model::unsearchable();
        } else {
            $model::whereIn((new $model)->getQualifiedKeyName(), $ids)->unsearchable();
        }
    }

    protected function updateSearchable(string $model, array $ids): void
    {
        $query = $model::where(fn ($query) => $model::makeAllSearchableUsing($query));

        if (empty($ids)) {
            $query->searchable();
        } else {
            $query->whereIn((new $model)->getQualifiedKeyName(), $ids)->searchable();
        }
    }

    protected function resolveModel(string $model): ?string
    {
        $modelClass = Arr::get(config('rapidez.models'), $model);

        if (! $modelClass || ! in_array(Searchable::class, class_uses_recursive($modelClass))) {
            return null;
        }

        return $modelClass;
    }
}
