<meta itemprop="mpn" content="{{ $product->id }}" />
<meta itemprop="sku" content="{{ $product->sku }}" />

@foreach($product->images as $image)
    <meta itemprop="image" content="{{ config('rapidez.media_url').'/catalog/product'.$image }}" />
@endforeach

<div itemprop="offers" itemtype="https://schema.org/Offer" itemscope>
    <meta itemprop="availability" content="https://schema.org/{{ $product->in_stock ? 'InStock' : 'OutOfStock' }}" />
    <meta itemprop="priceCurrency" content="@config('currency/options/default')" />
    <meta itemprop="price" content="{{ round($product->price, 2) }}" />
    <meta itemprop="url" content="{{ url($product->url) }}" />
</div>
