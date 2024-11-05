@php $dropdownClasses = '!font-sans !h-auto !py-3 !px-5 border !rounded-md !border-border outline-0 ring-0 text-sm transition-colors focus:!ring-transparent focus:!border-neutral disabled:cursor-not-allowed placeholder:!text-inactive' @endphp
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
