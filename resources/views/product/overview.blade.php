@extends('rapidez::layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('description', $product->meta_description)
@section('canonical', url($product->url))
@include('rapidez::layouts.partials.head.hreflang', ['alternates' => $product->alternates])

@section('content')
    <div class="container">
        @include('rapidez::product.partials.breadcrumbs')
        <div itemtype="https://schema.org/Product" itemscope>
            @include('rapidez::product.partials.microdata')
            @include('rapidez::product.partials.opengraph')
            <div class="relative flex max-lg:flex-col gap-10">
                <div class="flex-1 flex flex-col shrink-0">
                    @include('rapidez::product.partials.images')
                </div>
                <div class="flex flex-col gap-5 flex-1 shrink-0">
                    @includeWhen($product->type_id == 'grouped', 'rapidez::product.partials.grouped')
                    @includeWhen($product->type_id !== 'grouped', 'rapidez::product.partials.addtocart')
                    @if (App::providerIsLoaded('Rapidez\Reviews\ReviewsServiceProvider'))
                        @if ($product->reviews_score)
                            <x-dynamic-component component="rapidez-reviews::stars" :score="$product->reviews_score" :count="$product->reviews_count" />
                        @endif
                    @endif
                    <div>
                        <div class="border-t pt-5 text-lg font-bold">@lang('Description')</div>
                        <div class="prose text-muted" itemprop="description">
                            {!! $product->description !!}
                        </div>
                    </div>
                    <div>
                        <div class="mb-2 border-t pt-5 text-lg font-bold">@lang('Specifications')</div>
                        <dl class="flex flex-col text-muted *:rounded *:p-2 odd:*:bg odd:*:font-semibold odd:*:text even:*:pl-4">
                            <dt>ID</dt>
                            <dd>{{ $product->entity_id }}</dd>
                            <dt>SKU</dt>
                            <dd>{{ $product->sku }}</dd>
                            @foreach (config('rapidez.models.attribute')::getCachedWhere(fn($a) => $a['productpage']) as $attribute)
                                @if (($value = $product->{$attribute['code']}) && !is_object($value))
                                    <dt>{{ $attribute['name'] }}</dt>
                                    <dd>
                                        @php $output = is_array($value) ? implode(', ', $value) : $value @endphp
                                        {!! $attribute['html'] ? $output : e($output) !!}
                                    </dd>
                                @endif
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (App::providerIsLoaded('Rapidez\Reviews\ReviewsServiceProvider'))
        <div class="my-5 py-8 bg min-h-[515px]">
            <div class="container grid w-full grid-cols-1 gap-5 p-5 md:grid-cols-3">
                @include('rapidez-reviews::form', ['sku' => $product->sku])
                <div class="md:col-span-2">
                    @include('rapidez-reviews::reviews', [
                        'sku' => $product->sku,
                        'reviews_count' => $product->reviews_count,
                        'reviews_score' => $product->reviews_score,
                    ])
                </div>
            </div>
        </div>
    @endif

    <div class="container flex flex-col gap-5 mt-14">
        <x-rapidez::productlist
            title="Related products"
            field="entity_id"
            :value="$product->relation_ids"
        />
        <x-rapidez::productlist
            title="We found other products you might like!"
            field="entity_id"
            :value="$product->upsell_ids"
        />
    </div>
@endsection
