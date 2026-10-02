<?php

namespace Rapidez\Core\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Remove results from the default website when website view specific is found.
 */
class ForCurrentWebsiteWithoutLimitScope implements Scope
{
    use AppliesScopeWithoutLimit;

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
        return $this->applyScopeWithoutLimit($query, $model, $this->websiteIdColumn, $this->uniquePerWebsiteKeys, config('rapidez.website'));
    }
}
