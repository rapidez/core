<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteItem extends Model
{
    protected $table = 'quote_item';

    protected $primaryKey = 'item_id';

    /** @return BelongsTo<Store, QuoteItem> */
    public function store(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.store'));
    }

    /** @return BelongsTo<Quote, QuoteItem> */
    public function quote(): BelongsTo
    {
        // @phpstan-ignore-next-line
        return $this->belongsTo(config('rapidez.models.quote'));
    }

    /** @return BelongsTo<QuoteItem, QuoteItem> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_item_id');
    }

    /** @return HasMany<QuoteItem> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_item_id');
    }

    /** @return HasMany<QuoteItemOption> */
    public function options(): HasMany
    {
        // @phpstan-ignore-next-line
        return $this->hasMany(config('rapidez.models.quote_item_option'), 'item_id');
    }
}
