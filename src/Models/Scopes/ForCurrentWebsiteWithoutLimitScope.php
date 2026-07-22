<?php

namespace Rapidez\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

/**
 * Remove results from the default website when website view specific is found.
 */
class ForCurrentWebsiteWithoutLimitScope implements Scope
{
    public array $uniquePerWebsiteKeys;

    /**
     * @param  string|array  $uniquePerWebsiteKey  column(s) that may be duplicate across websites, but must be unique when filtered by website.
     */
    public function __construct(string|array $uniquePerWebsiteKey, public $websiteIdColumn = 'website_id')
    {
        $this->uniquePerWebsiteKeys = is_array($uniquePerWebsiteKey) ? $uniquePerWebsiteKey : [$uniquePerWebsiteKey];
    }

    public function apply(Builder $query, Model $model)
    {
        if (! config('rapidez.website')) {
            return $query
                ->where($query->qualifyColumn($this->websiteIdColumn), 0);
        }

        $scope = $this;

        return $query
            // Pre-filter results to be default and current website only.
            ->whereIn($query->qualifyColumn($this->websiteIdColumn), [0, config('rapidez.website')])
            // Remove values from the default website where values for the current website exist.
            ->where(fn ($query) => $query
                // Remove values where we already have values in the current website.
                ->where(function ($query) use ($scope, $model) {
                    $query
                        ->whereNotExists(function ($query) use ($scope, $model) {
                            $query
                                ->select(DB::raw(1))
                                ->from($model->getTable() . ' as comparison')
                                ->where('comparison.' . $this->websiteIdColumn, config('rapidez.website'));
                            foreach ($scope->uniquePerWebsiteKeys as $uniquePerWebsiteKey) {
                                $query->whereColumn('comparison.' . $uniquePerWebsiteKey, $model->qualifyColumn($uniquePerWebsiteKey));
                            }
                        });
                })
                // Unless the value IS the current website.
                ->orWhere($query->qualifyColumn($this->websiteIdColumn), config('rapidez.website'))
            );
    }
}
