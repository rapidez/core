@extends('rapidez::layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('description', $product->meta_description)

@section('content')
    <div class="container mx-auto">
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
                    @if (App::providerIsLoaded('Rapidez\Reviews\ReviewsServiceProvider'))
                        @if($product->reviews_score)
                            <stars :score="{{ $product->reviews_score }}" :count="{{ $product->reviews_count }}"></stars>
                        @endif
                    @endif
                    <div>
                        <div class="border-t pt-5 text-lg font-bold">@lang('Description')</div>
                        <div class="prose text-secondary" itemprop="description">
                            {!! $product->description !!}
                        </div>
                    </div>
                    <div>
                        <div class="mb-2 border-t pt-5 text-lg font-bold">@lang('Specifications')</div>
                        <div class="flex flex-col text-secondary">
                            <div class="rounded p-2 odd:bg-highlight odd:font-semibold odd:text-primary even:pl-4">ID</div>
                            <div class="rounded p-2 odd:bg-highlight odd:font-semibold odd:text-primary even:pl-4">{{ $product->id }}</div>
                            <div class="rounded p-2 odd:bg-highlight odd:font-semibold odd:text-primary even:pl-4">SKU</div>
                            <div class="rounded p-2 odd:bg-highlight odd:font-semibold odd:text-primary even:pl-4">{{ $product->sku }}</div>
                            @foreach (config('rapidez.models.attribute')::getCachedWhere(fn($a) => $a['productpage']) as $attribute)
                                @if (($value = $product->{$attribute['code']}) && !is_object($value))
                                    <div class="rounded p-2 odd:bg-highlight odd:font-semibold odd:text-primary even:pl-4">{{ $attribute['name'] }}</div>
                                    <div class="rounded p-2 odd:bg-highlight odd:font-semibold odd:text-primary even:pl-4">
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
    </div>
    <div class="bg-highlight my-5">
        <div class="container mx-auto grid sm:grid-cols-3 gap-5 w-full grid-cols-1 p-5">
            <div class="sm:sticky top-5 h-fit bg-white rounded p-4 w-full">
                @include('rapidez-reviews::form', ['sku' => $product->sku])
            </div>
            <div class="col-span-2">
                @include('rapidez-reviews::reviews', [
                    'sku' => $product->sku,
                    'reviews_count' => $product->reviews_count,
                    'reviews_score' => $product->reviews_score,
                ])
            </div>
        </div>
    </div>
    <div class="container mx-auto">
        <x-rapidez::productlist title="Related products" field="id" :value="$product->relation_ids" />
        <x-rapidez::productlist title="We found other products you might like!" field="id" :value="$product->upsell_ids" />
    </div>
@endsection
