<reactive-list
    component-id="products"
    data-field="name.keyword"
    list-class="flex flex-wrap mt-5 -mx-1 overflow-hidden"
    :pagination="true"
    :from="0"
    :size="isNaN(parseInt($root.config.grid_per_page)) ? 10000 : parseInt($root.config.grid_per_page)"
    :react="{and: reactiveFilters}"
    :sort-options="sortOptions"
    :inner-class="{
        button: '!bg-primary disabled:!bg-secondary',
        sortOptions: '{{ config('rapidez.sortOptions') }}'
    }"
    prev-label="@lang('Prev')"
    next-label="@lang('Next')"
    u-r-l-params
>
    @include('rapidez::listing.partials.stats')
    @include('rapidez::listing.partials.item')
    @include('rapidez::listing.partials.no-results')
</reactive-list>
