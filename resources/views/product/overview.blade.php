@extends('rapidez::layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('description', $product->meta_description)

@section('content')
    <h1 class="font-bold text-4xl mb-5">{{ $product->name }}</h1>

    <div class="flex flex-col sm:flex-row mb-5">
        <div class="sm:w-2/3 sm:mr-5">
            <div class="flex flex-wrap items-center">
                @forelse($product->images as $image)
                    <img
                        src="/storage/resizes/467/catalog/product{{ $image->value }}" alt="{{ $product->name }}"
                        class="{{ $product->images->count() == 1 ? 'w-full sm:w-1/2 rounded' : 'w-1/2' }}"
                        loading="lazy"
                    />
                @empty
                    <x-rapidez::no-image class="rounded h-64"/>
                @endforelse
            </div>
        </div>
        <div class="sm:w-1/3">
            <div class="p-3 my-5 sm:mt-0 bg-gray-200 rounded">
                {!! $product->description !!}
            </div>
            @include('rapidez::product.partials.addtocart')
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
@endsection
