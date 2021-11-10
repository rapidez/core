@extends('rapidez::layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('description', $product->meta_description)

@section('content')
    @include('rapidez::product.partials.breadcrumbs')

    <div itemtype="https://schema.org/Product" itemscope>
        <h1 class="font-bold text-4xl mb-5" itemprop="name">{{ $product->name }}</h1>

        @include('rapidez::product.partials.microdata')

        <div class="flex flex-col sm:flex-row mb-5">
            <div class="sm:w-1/2 sm:mr-5">
                @include('rapidez::product.partials.images')
            </div>
            <div class="sm:w-1/2">
                <div class="p-3 my-5 sm:mt-0 bg-gray-200 rounded prose prose-green max-w-none" itemprop="description">
                    {!! $product->description !!}
                </div>
                @include('rapidez::product.partials.addtocart')
            </div>
        </div>

        <dl class="flex flex-wrap w-full bg-gray-200 rounded p-3 prose prose-green max-w-none">
            <dt class="w-1/2 sm:w-1/4 font-bold">ID</dt>
            <dd class="w-1/2 sm:w-3/4">{{ $product->id }}</dd>
            <dt class="w-1/2 sm:w-1/4 font-bold">SKU</dt>
            <dd class="w-1/2 sm:w-3/4" itemprop="sku">{{ $product->sku }}</dd>
            @foreach(config('rapidez.models.attribute')::getCachedWhere(fn ($a) => $a['productpage']) as $attribute)
                @if(($value = $product->{$attribute['code']}) && !is_object($value))
                    <dt class="w-1/2 sm:w-1/4 font-bold">{{ $attribute['name'] }}</dt>
                    <dd class="w-1/2 sm:w-3/4">
                        @php $output = is_array($value) ? implode(', ', $value) : $value @endphp
                        {!! $attribute['html'] ? $output : e($output) !!}
                    </dd>
                @endif
            @endforeach
        </dl>
    </div>

    <x-rapidez::productlist title="Related products" field="id" :value="$product->relation_ids"/>
    <x-rapidez::productlist title="We found other products you might like!" field="id" :value="$product->upsell_ids"/>
@endsection
