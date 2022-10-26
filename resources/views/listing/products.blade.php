@php $dropdownClasses = '!outline-none !rounded shadow focus:ring focus:ring-green-500 h-[32px] pr-[25px] pl-[10px] pb-0 pt-0 text-[0.82rem]'; @endphp
<reactive-list
    id="products"
    component-id="products"
    data-field="name.keyword"
    list-class="flex flex-wrap mt-5 -mx-1 overflow-hidden"
    :pagination="true"
    v-on:page-click="scrollToElement('#products')"
    :size="isNaN(parseInt($root.config.grid_per_page)) ? 10000 : parseInt($root.config.grid_per_page)"
    :react="{and: reactiveFilters}"
    :sort-options="sortOptions"
    :inner-class="{
        button: '!bg-primary disabled:!bg-secondary',
        sortOptions: '{{ $dropdownClasses }}'
    }"
    prev-label="@lang('Prev')"
    next-label="@lang('Next')"
    u-r-l-params
>
    @include('rapidez::listing.partials.stats', compact('dropdownClasses'))
    @include('rapidez::listing.partials.item')
    @include('rapidez::listing.partials.no-results')
</reactive-list>
