<?php

namespace Rapidez\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait AppliesScopeWithoutLimit
{
    protected function applyScopeWithoutLimit(Builder $query, Model $model, string $idColumn, array $uniqueKeys, mixed $currentScopeId): Builder
    {
        if (! $currentScopeId) {
            return $query
                ->where($query->qualifyColumn($idColumn), 0);
        }

        return $query
            // Pre-filter results to be default and current scope only.
            ->whereIn($query->qualifyColumn($idColumn), [0, $currentScopeId])
            // Remove values from the default scope where values for the current scope exist.
            ->where(fn ($query) => $query
                // Remove values where we already have values in the current scope.
                ->where(function ($query) use ($model, $idColumn, $uniqueKeys, $currentScopeId) {
                    $query
                        ->whereNotExists(function ($query) use ($model, $idColumn, $uniqueKeys, $currentScopeId) {
                            $query
                                ->select(DB::raw(1))
                                ->from($model->getTable() . ' as comparison')
                                ->where('comparison.' . $idColumn, $currentScopeId);
                            foreach ($uniqueKeys as $uniqueKey) {
                                $query->whereColumn('comparison.' . $uniqueKey, $model->qualifyColumn($uniqueKey));
                            }
                        });
                })
                // Unless the value IS the current scope.
                ->orWhere($query->qualifyColumn($idColumn), $currentScopeId)
            );
    }
}
