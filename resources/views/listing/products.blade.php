@php $dropdownClasses = '!h-auto !border-solid !border !border-border !rounded !py-2 !ring-0 focus:!border-inactive !text-sm !text-neutral !outline-none ' @endphp
<reactive-list
    id="products"
    component-id="products"
    data-field="name.keyword"
    list-class="flex flex-wrap mt-5 -mx-4 sm:-mx-1 overflow-hidden"
    :pagination="true"
    v-on:page-click="scrollToElement('#products')"
    :size="isNaN(parseInt(listingSlotProps.pageSize)) ? 10000 : parseInt(listingSlotProps.pageSize)"
    :react="{and: reactiveFilters}"
    :sort-options="sortOptions"
    :inner-class="{
        button: 'pagination-button',
        current: 'current-button',
        sortOptions: '{{ $dropdownClasses }}',
        pagination: 'pagination'
    }"
    prev-label="@lang('Prev')"
    next-label="@lang('Next')"
    u-r-l-params
>
    @include('rapidez::listing.partials.stats', compact('dropdownClasses'))
    @include('rapidez::listing.partials.item')
    @include('rapidez::listing.partials.no-results')
</reactive-list>
