<?php

namespace Rapidez\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Rapidez\Core\Actions\DecodeJwt;
use Rapidez\Core\Casts\CommaSeparatedToIntegerArray;
use Rapidez\Core\Casts\QuoteItems;
use Rapidez\Core\Models\Scopes\IsActiveScope;
use TorMorten\Eventy\Facades\Eventy;

class Quote extends Model
{
    protected $table = 'quote';

    protected $primaryKey = 'entity_id';

    protected $guarded = [];

    protected $casts = [
        'items'       => QuoteItems::class,
        'cross_sells' => CommaSeparatedToIntegerArray::class,
    ];

    protected static function booting()
    {
        static::addGlobalScope(new IsActiveScope);
        static::addGlobalScope('with-all-information', function (Builder $builder) {
            $builder
                ->addSelect($builder->qualifyColumns([
                    'entity_id',
                    'items_count',
                    'items_qty',
                ]))
                ->selectRaw('
                    MAX(quote_address.subtotal_incl_tax) as subtotal,
                    MAX(quote_address.subtotal) as subtotal_excl_tax,
                    MAX(quote_address.tax_amount) as tax,
                    MAX(quote_address.shipping_incl_tax) as shipping_amount,
                    MAX(quote_address.shipping_amount) as shipping_amount_excl_tax,
                    MAX(quote_address.shipping_method) as shipping_method,
                    MAX(quote_address.shipping_description) as shipping_description,
                    MAX(quote_address.grand_total) as total,
                    MIN(quote_address.discount_amount) as discount_amount,
                    MAX(quote_address.discount_description) as discount_name,
                    GROUP_CONCAT(DISTINCT cross_sell.linked_product_id) as cross_sells
                ')
                ->selectRaw('JSON_REMOVE(JSON_OBJECTAGG(IFNULL(quote_item.item_id, "null__"), JSON_OBJECT(
                    ' . Eventy::filter('quote.items.select', <<<'QUERY'
                        "item_id", quote_item.item_id,
                        "product_id", quote_item.product_id,
                        "sku", quote_item.sku,
                        "name", quote_item.`name`,
                        "image", product.thumbnail,
                        "url_key", product.url_key,
                        "qty", quote_item.qty,
                        "qty_increments", IF(enable_qty_increments, stock.qty_increments, 1),
                        "min_sale_qty", stock.min_sale_qty,
                        "price", quote_item.price_incl_tax,
                        "price_excl_tax", quote_item.price,
                        "total", quote_item.row_total_incl_tax,
                        "total_excl_tax", quote_item.row_total,
                        "attributes", quote_item_option.value,
                        "type", quote_item.product_type
                    QUERY) . '
                )), "$.null__") AS items')
                ->leftJoin('quote_id_mask', 'quote_id_mask.quote_id', '=', $builder->getModel()->getQualifiedKeyName())
                ->leftJoin('oauth_token', 'oauth_token.customer_id', '=', $builder->qualifyColumn('customer_id'))
                ->leftJoin('quote_address', function ($join) use ($builder) {
                    $join->on('quote_address.quote_id', '=', $builder->getModel()->getQualifiedKeyName());
                })
                ->leftJoin('quote_item', function ($join) use ($builder) {
                    $join->on('quote_item.quote_id', '=', $builder->getModel()->getQualifiedKeyName())->whereNull('quote_item.parent_item_id');
                })
                ->leftJoin('quote_item_option', function ($join) {
                    $join->on('quote_item.item_id', '=', 'quote_item_option.item_id')->where('code', 'attributes');
                })
                ->leftJoin('catalog_product_flat_' . config('rapidez.store') . ' AS product', 'product.entity_id', '=', 'quote_item.product_id')
                ->leftJoin('cataloginventory_stock_item AS stock', 'stock.product_id', '=', 'quote_item.product_id')
                ->leftJoin('catalog_product_link AS cross_sell', function ($join) {
                    $join->on('cross_sell.product_id', '=', 'quote_item.product_id')->where('cross_sell.link_type_id', '=', 5);
                })
                ->groupBy($builder->getModel()->getQualifiedKeyName());
        });
    }

    public function store()
    {
        return $this->belongsTo(config('rapidez.models.store'));
    }

    public function sales_order()
    {
        return $this->belongsTo(config('rapidez.models.sales_order'));
    }

    // Named it like this as "items" is already in use to keep it backwards compatible.
    // This will be removed when we migrate the cart to GraphQL.
    public function items2()
    {
        return $this->hasMany(config('rapidez.models.quote_item'), 'quote_id');
    }

    public function scopeWhereQuoteIdOrCustomerToken(Builder $query, string $quoteIdMaskOrCustomerToken)
    {
        $query->when(
            DecodeJwt::isJwt($quoteIdMaskOrCustomerToken),
            fn (Builder $query) => $query
                ->where(
                    $this->qualifyColumn('customer_id'),
                    DecodeJwt::decode($quoteIdMaskOrCustomerToken)
                        ->claims()
                        ->get('uid')
                ),
            fn (Builder $query) => $query
                ->where('masked_id', $quoteIdMaskOrCustomerToken)
                ->orWhere('token', $quoteIdMaskOrCustomerToken)
        );
    }
}
