@push('head')
<meta property="og:type" content="og:product" />
<meta property="og:title" content="{{ $product->name }}" />
@if ($product->meta_description)
<meta property="og:description" content="{{ $product->meta_description }}" />
@endif
<meta property="og:url" content="{{ url($product->url) }}"/>
@isset($product->images[0])
<meta property="og:image" content="{{ config('rapidez.media_url').'/catalog/product'.$product->images[0] }}"/>
@endisset
<meta property="og:site_name" content="{{ config('app.name') }}" />
<meta property="product:price:amount" content="{{ round($product->price, 2) }}" />
<meta property="product:price:currency" content="@config('currency/options/default')" />
<meta property="og:availability" content="{{ $product->in_stock ? 'instock' : 'out of stock' }}" />
@endpush
