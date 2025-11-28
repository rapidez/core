<?php

namespace Rapidez\Core\Models\Traits;

trait HasToArrayData
{
    public function toArray(): array
    {
        return array_merge(
            array_merge(
                ...collect(get_class_methods($this))
                    ->filter(fn ($function) => str_ends_with($function, 'ToArrayData'))
                    ->map(fn ($function) => $this->{$function}())
                    ->toArray(),
            ),
            parent::toArray()
        );
    }
}
