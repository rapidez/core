@extends('rapidez::layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('description', $product->meta_description)

@section('content')
    <div class="container mx-auto text-gray-800">
        @include('rapidez::product.partials.breadcrumbs')
        <div itemtype="https://schema.org/Product" itemscope>
            @include('rapidez::product.partials.microdata')
            @include('rapidez::product.partials.opengraph')
            <div class="flex flex-col gap-8 sm:flex-row">
                <div class="sm:w-1/2">
                    <div class="sticky top-5">
                        @include('rapidez::product.partials.images')
                    </div>
                </div>
                <div class="flex flex-col gap-5 sm:w-1/2">
                    @includeWhen($product->type == 'grouped', 'rapidez::product.partials.grouped')
                    @includeWhen($product->type !== 'grouped', 'rapidez::product.partials.addtocart')
                    <div>
                        <div class="border-t pt-5 text-lg font-bold">@lang('Description')</div>
                        <div class="prose text-gray-500" itemprop="description">
                            {!! $product->description !!}
                        </div>
                    </div>
                    <div>
                        <div class="mb-2 border-t pt-5 text-lg font-bold">@lang('Specifications')</div>
                        <div class="flex flex-col text-gray-500">
                            <div class="rounded p-2 odd:bg-gray-100 odd:font-semibold odd:text-gray-800 even:pl-4">ID</div>
                            <div class="rounded p-2 odd:bg-gray-100 odd:font-semibold odd:text-gray-800 even:pl-4">{{ $product->id }}</div>
                            <div class="rounded p-2 odd:bg-gray-100 odd:font-semibold odd:text-gray-800 even:pl-4">SKU</div>
                            <div class="rounded p-2 odd:bg-gray-100 odd:font-semibold odd:text-gray-800 even:pl-4">{{ $product->sku }}</div>
                            @foreach (config('rapidez.models.attribute')::getCachedWhere(fn($a) => $a['productpage']) as $attribute)
                                @if (($value = $product->{$attribute['code']}) && !is_object($value))
                                    <div class="rounded p-2 odd:bg-gray-100 odd:font-semibold odd:text-gray-800 even:pl-4">{{ $attribute['name'] }}</div>
                                    <div class="rounded p-2 odd:bg-gray-100 odd:font-semibold odd:text-gray-800 even:pl-4">
                                        @php $output = is_array($value) ? implode(', ', $value) : $value @endphp
                                        {!! $attribute['html'] ? $output : e($output) !!}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-rapidez::productlist title="Related products" field="id" :value="$product->relation_ids" />
        <x-rapidez::productlist title="We found other products you might like!" field="id" :value="$product->upsell_ids" />
    </div>
@endsection
