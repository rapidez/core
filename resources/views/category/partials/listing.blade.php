<reactive-list
    component-id="products"
    data-field="name.keyword"
    list-class="flex flex-wrap mt-5 -mx-1 overflow-hidden"
    :pagination="true"
    :from="0"
    :size="32"
    :react="{and: reactiveFilters}"
    :sort-options="sortOptions"
    :inner-class="{
        button: 'btn btn-pagination',
        sortOptions: 'sort-options'
    }"
    u-r-l-params
>
    @include('rapidez::category.partials.listing.stats')
    @include('rapidez::category.partials.listing.item')
    @include('rapidez::category.partials.listing.no-results')
</reactive-list>
