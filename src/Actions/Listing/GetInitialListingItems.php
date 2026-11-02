<?php

namespace Rapidez\Core\Actions\Listing;

use Rapidez\Core\Facades\Rapidez;
use Throwable;

class GetInitialListingItems
{
    public function forCategory(int $categoryId, ?int $limit = null): array
    {
        return $this->search(
            sprintf('(visibility:(2 OR 4) OR (NOT _exists_:visibility)) AND (category_ids:%d)', $categoryId),
            $limit ?? $this->defaultLimit(),
        );
    }

    public function forSearch(?string $query, ?int $limit = null): array
    {
        $visibility = '(visibility:(3 OR 4) OR (NOT _exists_:visibility))';
        $query = $query ? trim(str_replace('"', '', $query)) : null;

        return $this->search($query ? "{$visibility} AND ({$query})" : $visibility, $limit ?? $this->defaultLimit());
    }

    public function forValues(string $field, array $values, ?int $limit = null): array
    {
        if (! count($values)) {
            return [];
        }

        return $this->search(sprintf('%s:(%s)', $field, implode(' OR ', $values)), $limit ?? count($values));
    }

    protected function search(string $queryString, int $limit): array
    {
        try {
            $raw = config('rapidez.models.product')::search($queryString)->take($limit)->raw();

            return collect($raw['hits']['hits'] ?? [])->pluck('_source')->toArray();
        } catch (Throwable $e) {
            report($e);

            return [];
        }
    }

    protected function defaultLimit(): int
    {
        return (int) Rapidez::config('catalog/frontend/grid_per_page', 12);
    }
}
