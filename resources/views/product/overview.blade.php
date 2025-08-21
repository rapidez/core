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
            @if (App::providerIsLoaded('Rapidez\Reviews\ReviewsServiceProvider'))
                @include('rapidez-reviews::components.metadata')
            @endif
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
        <div class="container my-5">
            @include('rapidez-reviews::reviews')
        </div>
    @endif

    @if (($product->relation_ids ?? false) || ($product->upsell_ids ?? false))
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
    @endif
@endsection
