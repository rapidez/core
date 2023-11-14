<meta itemprop="mpn" content="{{ $product->entity_id }}" />
<meta itemprop="sku" content="{{ $product->sku }}" />

@foreach($product->images as $image)
    <meta itemprop="image" content="{{ config('rapidez.media_url').'/catalog/product'.$image }}" />
@endforeach

<div itemprop="offers" itemtype="https://schema.org/Offer" itemscope>
    <meta itemprop="availability" content="https://schema.org/{{ $product->in_stock ? 'InStock' : 'OutOfStock' }}" />
    <meta itemprop="priceCurrency" content="@config('currency/options/default')" />
    <meta itemprop="price" content="{{ round($product->special_price ?: $product->price, 2) }}" />
    <meta itemprop="url" content="{{ url($product->url) }}" />
    @if($product->special_to_date && $product->special_to_date > now()->toDateTimeString())
        <meta itemprop="priceValidUntil" content="{{ $product->special_to_date }}" />
    @endif
</div>
