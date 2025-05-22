@extends('rapidez::layouts.app')

@section('title', $category->meta_title ?: $category->name)
@section('description', $category->meta_description)
@section('canonical', url($category->url))
@include('rapidez::layouts.partials.head.hreflang', ['alternates' => $category->alternates])

@section('content')
    <div class="container">
        @include('rapidez::category.partials.breadcrumbs')

        <h1 class="text-2xl font-medium mb-5">{{ $category->name }}</h1>

        @if ($category->is_anchor)
            @if (!$category->products()->exists())
                @include('rapidez::listing.partials.no-products')
            @else
                <x-rapidez::listing
                    :root-path="$category->parentcategories->pluck('name')"
                    v-bind:category-id="{{ $category->entity_id }}"
                />
            @endif
        @else
            <div class="flex gap-x-20 gap-y-3 max-lg:flex-col">
                <div class="lg:w-80 shrink-0">
                    @widget('sidebar.main', 'anchor_categories', 'catalog_category_view_type_layered', $category->entity_id)
                </div>
                <div class="flex-1">
                    @widget('content.top', 'anchor_categories', 'catalog_category_view_type_layered', $category->entity_id)
                </div>
            </div>
        @endif

        <div class="prose prose-green max-w-none">
            {!! $category->description !!}
        </div>
    </div>
@endsection
