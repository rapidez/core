@extends('rapidez::layouts.app')

@section('title', $category->meta_title ?: $category->name)
@section('description', $category->meta_description)
@section('canonical', url($category->url))
@include('rapidez::layouts.partials.head.hreflang', ['alternates' => $category->alternates])

@section('content')
    <div class="container">
        @include('rapidez::category.partials.breadcrumbs')

        @if ($category->is_anchor)
            <x-rapidez::listing
                :root-path="$category->parentcategories->pluck('name')->join(' > ')"
                v-bind:category-id="{{ $category->entity_id }}"
            >
                <x-slot:title>
                    <h1 class="text-2xl font-medium">{{ $category->name }}</h1>
                </x-slot:title>
            </x-rapidez::listing>
        @else
            <div class="flex gap-x-20 gap-y-3 max-lg:flex-col">
                <div class="lg:w-80 shrink-0">
                    <h1 class="mb-5 text-3xl font-medium">{{ $category->name }}</h1>
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
