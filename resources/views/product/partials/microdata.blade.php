<meta itemprop="mpn" content="{{ $product->id }}" />

@foreach($product->images as $image)
    <link itemprop="image" href="{{ config('rapidez.media_url').'/catalog/product'.$image }}" />
@endforeach

<div itemprop="offers" itemtype="https://schema.org/Offer" itemscope>
    <meta itemprop="availability" content="https://schema.org/{{ $product->in_stock ? 'InStock' : 'OutOfStock' }}" />
    <meta itemprop="priceCurrency" content="@config('currency/options/default')" />
    <meta itemprop="price" content="{{ $product->price }}" />
    <meta itemprop="url" content="{{ url($product->url) }}" />
</div>
