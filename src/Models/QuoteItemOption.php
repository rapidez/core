<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use TorMorten\Eventy\Facades\Eventy;

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
            get: function (string $value) {
                $value = Eventy::filter('quote_item_option.value', $value, $this);

                if (isset($this->option) && in_array($this->option->type, ['drop_down', 'radio'])) {
                    $value = config('rapidez.models.product_option_type_value')::find($value)->title;
                }

                if ($this->code === 'option_ids') {
                    $value = explode(',', $value);
                }

                return is_string($value) ? (json_decode($value) ?? $value) : $value;
            }
        )->shouldCache();
    }


    protected function label(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->option?->titles->firstForCurrentStore()->title
        )->shouldCache();
    }

    // It would be nice if we could make a HasMany relation from this so it's possible
    // to eager load it but DB::raw() to do the explode within SQL can't be used.
    protected function option(): Attribute
    {
        return Attribute::make(
            get: fn () => config('rapidez.models.product_option')::find(explode('_', $this->code)[1] ?? $this->code)
        )->shouldCache();
    }
}
