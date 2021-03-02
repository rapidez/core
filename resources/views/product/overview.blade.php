@extends('rapidez::layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('description', $product->meta_description)

@section('content')
    <h1 class="font-bold text-4xl mb-5">{{ $product->name }}</h1>
    <product images="{{ $product->images }}">
        <div class="flex flex-col sm:flex-row mb-5" slot-scope="{ changeAttribute, productImages }">
            <div class="sm:w-2/3 sm:mr-5">
                <div v-cloak v-if="productImages.length" :class="productImages.length === 1 ? '' : 'grid gap-3 grid-cols-2'">
                    <div v-for="image in productImages" class="flex items-center justify-center border rounded p-5 h-48 sm:h-64 md:h-80 lg:h-96">
                            <img
                                :src="'/storage/resizes/450/catalog/product' + image"
                                alt="{{ $product->name }}"
                                class="max-h-full max-w-full"
                                loading="lazy"
                            />
                        </div>
                        <x-rapidez::no-image v-if="productImages.length === 0" class="rounded h-64"/>
                </div>
            </div>
            <div class="sm:w-1/3">
                <div class="p-3 my-5 sm:mt-0 bg-gray-200 rounded">
                    {!! $product->description !!}
                </div>
                @include('rapidez::product.partials.addtocart')
            </div>
        </div>
    </div>

        <dl class="flex flex-wrap w-full bg-gray-200 rounded p-3">
            <dt class="w-1/2 sm:w-1/4 font-bold">ID</dt>
            <dd class="w-1/2 sm:w-3/4">{{ $product->id }}</dd>
            <dt class="w-1/2 sm:w-1/4 font-bold">SKU</dt>
            <dd class="w-1/2 sm:w-3/4">{{ $product->sku }}</dd>
            @foreach(['style_general', 'pattern', 'climate', 'activity', 'style_bags', 'material', 'strap_bags', 'features_bags', 'gender', 'category_gear', 'format', 'style_bottom', 'style_general'] as $attribute)
                @if($product->$attribute)
                    <dt class="w-1/2 sm:w-1/4 font-bold">{{ ucfirst(str_replace('_', ' ', $attribute)) }}</dt>
                    <dd class="w-1/2 sm:w-3/4">{{ is_array($product->$attribute) ? implode(', ', $product->$attribute) : $product->$attribute }}</dd>
                @endif
            @endforeach
        </dl>
    </product>
@endsection
