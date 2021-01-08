@extends('rapidez::layouts.app')

@section('title', $category->meta_title ?: $category->name)
@section('description', $category->meta_description)

@section('content')
    <h1 class="font-bold text-3xl">{{ $category->name }}</h1>

    @if($category->is_anchor)
        <category v-cloak>
            <div
                slot-scope="{ loaded, baseStyles, filters, reactiveFilters, sortOptions, categoryQuery, onChange }"
                :style="baseStyles"
            >
                <reactive-base
                    :app="config.es_prefix + '_products_' + config.store"
                    :url="config.es_url"
                    v-if="loaded"
                >
                    <selected-filters></selected-filters>
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-1/5">
                            @include('rapidez::category.partials.filters')
                        </div>
                        <div class="md:w-4/5">
                            @include('rapidez::category.partials.listing')
                        </div>
                    </div>
                </reactive-base>
            </div>
        </category>
    @else
        <div class="flex flex-col md:flex-row">
            <div class="md:w-1/5">
                @widget('sidebar.main', 'anchor_categories', 'catalog_category_view_type_layered', $category->entity_id)
            </div>
            <div class="md:w-4/5">
                @widget('content.top', 'anchor_categories', 'catalog_category_view_type_layered', $category->entity_id)
            </div>
        </div>
    @endif

    {!! str_replace('<h2>', '<h2 class="font-bold text-2xl">', $category->description) !!}
@endsection

@push('page_end')
    <product-compare-widget
        class-product="py-2 border-b border-primary"
    />
@endpush
