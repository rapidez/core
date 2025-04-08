@extends('rapidez::layouts.app')

@section('title', $category->meta_title ?: $category->name)
@section('description', $category->meta_description)
@section('canonical', url($category->url))
@include('rapidez::layouts.partials.head.hreflang', ['alternates' => $category->alternates])

@section('content')
    <div class="container">
        @include('rapidez::category.partials.breadcrumbs')
        <h1 class="mb-5 text-3xl font-bold">{{ $category->name }}</h1>

        @if ($category->is_anchor)
            <x-rapidez::listing
                :root-path="$category->parentcategories->pluck('name')->join(' > ')"
                filter-query-string="visibility:(2 OR 4) AND category_ids:{{ $category->entity_id }}"
                v-bind:base-filters="() => [ categoryPositions({{ $category->entity_id }}) ]"
            />
        @else
            <div class="flex max-md:flex-col">
                <div class="xl:w-1/5">
                    @widget('sidebar.main', 'anchor_categories', 'catalog_category_view_type_layered', $category->entity_id)
                </div>
                <div class="xl:w-4/5">
                    @widget('content.top', 'anchor_categories', 'catalog_category_view_type_layered', $category->entity_id)
                </div>
            </div>
        @endif

        <div class="prose prose-green max-w-none">
            {!! $category->description !!}
        </div>
    </div>
@endsection
