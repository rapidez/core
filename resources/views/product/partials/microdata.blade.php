<meta itemprop="name" content="{{ $product->name }}" />
<meta itemprop="mpn" content="{{ $product->entity_id }}" />
<meta itemprop="sku" content="{{ $product->sku }}" />
@if ($product->description)
    <meta itemprop="description" content="{{ str($product->description)->stripTags()->squish() }}" />
@endif

@foreach ($product->images as $image)
    <meta itemprop="image" content="{{ config('rapidez.media_url').'/catalog/product'.$image }}" />
@endforeach

<div itemprop="offers" itemtype="https://schema.org/Offer" itemscope>
    <meta itemprop="availability" content="https://schema.org/{{ $product->stock->is_in_stock ? 'InStock' : 'OutOfStock' }}" />
    <meta itemprop="itemCondition" content="https://schema.org/NewCondition" />
    <meta itemprop="priceCurrency" content="@config('currency/options/default')" />
    <meta itemprop="price" content="{{ round($product->special_price ?: $product->price, 2) }}" />
    <meta itemprop="url" content="{{ url($product->url) }}" />
    @if ($product->special_to_date && $product->special_to_date > now()->toDateTimeString())
        <meta itemprop="priceValidUntil" content="{{ str($product->special_to_date)->before(' ')->before('T') }}" />
    @endif
</div>
