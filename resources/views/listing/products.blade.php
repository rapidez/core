@include('rapidez::listing.partials.stats')

<ais-hits>
    <template v-slot="{ items }">
        <div class="flex flex-wrap mt-5 -mx-4 sm:-mx-1 overflow-hidden">
            <template v-for="(item, count) in items">
                @include('rapidez::listing.partials.item')
            </template>
        </div>
    </template>
</ais-hits>

@include('rapidez::listing.partials.pagination')



{{--
@php $dropdownClasses = '!h-auto !py-3 !px-5 !border-solid !border !border-default !rounded-md !py-2 !ring-0 focus:!border-muted !text-sm !text !outline-none max-md:w-full' @endphp
<reactive-list
    id="products"
    class="*:flex-wrap *:gap-3 *:max-sm:gap-y-3 *:max-md:justify-end"
    list-class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-2 md:gap-5 mt-5 overflow-hidden"
    v-on:page-click="scrollToElement('#products')"
    :size="isNaN(parseInt(listingSlotProps.pageSize)) ? 10000 : parseInt(listingSlotProps.pageSize)"
    :sort-options="sortOptions"
    :inner-class="{
        sortOptions: '{{ $dropdownClasses }}',
    }"
>
    @include('rapidez::listing.partials.stats', compact('dropdownClasses'))
    @include('rapidez::listing.partials.item')
    @include('rapidez::listing.partials.no-results')
</reactive-list>
 --}}
