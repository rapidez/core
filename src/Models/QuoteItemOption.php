<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

class QuoteItemOption extends Model
{
    protected $table = 'quote_item_option';

    protected $primaryKey = 'option_id';

    public $timestamps = false;

    protected $appends = ['label'];

    public function quote_item()
    {
        return $this->belongsTo(config('rapidez.models.quote_item'), 'item_id');
    }

    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => match ($this->code) {
                'info_buyRequest' => json_decode($value),
                'option_ids' => explode(',', $value),
                default => (function () use ($value) {
                    if (in_array($this->option->type, ['drop_down'])) {
                        return ProductOptionTypeValue::find($value)
                            ->titles()
                            ->whereIn('store_id', [0, config('rapidez.store')])
                            ->orderByDesc('store_id')
                            ->first()->title;
                    }

                    if ($this->option->type == 'file') {
                        return json_decode($value);
                    }

                    return $value;
                })()
            })->shouldCache();
    }

    protected function label(): Attribute
    {
        return Attribute::make(
            get: fn () => $this
                ->option
                ?->titles()
                ->whereIn('store_id', [0, config('rapidez.store')])
                ->first()
                ->title
        )->shouldCache();
    }

    // It would be nice if we could make a HasMany relation from this so it's possible
    // to eager load it but DB::raw() to do the explode within SQL can't be used.
    protected function option(): Attribute
    {
        return Attribute::make(
            get: fn () => ProductOption::find(explode('_', $this->code)[1])
        )->shouldCache();
    }
}
