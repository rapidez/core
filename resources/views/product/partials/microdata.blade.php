@php
    $description = $product->description
        ? str(html_entity_decode(strip_tags($product->description), ENT_QUOTES | ENT_HTML5))->squish()
        : null;
@endphp

<meta itemprop="name" content="{{ $product->name }}" />
<meta itemprop="mpn" content="{{ $product->entity_id }}" />
<meta itemprop="sku" content="{{ $product->sku }}" />
@if ($description)
    <meta itemprop="description" content="{{ $description }}" />
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
        <meta itemprop="priceValidUntil" content="{{ \Illuminate\Support\Carbon::parse($product->special_to_date)->toDateString() }}" />
    @endif
</div>
