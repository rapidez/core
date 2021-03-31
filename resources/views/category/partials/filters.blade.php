<x-rapidez::slideover v-on-click-away="close" :mobile-only="true">
    <x-slot name="button">
        <button type="button" class="md:hidden btn btn-primary w-full mb-3" v-on:click="toggle">@lang('Filters')</button>
    </x-slot>

    <x-slot name="title">
        @lang('Filters')
    </x-slot>

    <reactive-component component-id="category" v-if="config.category">
        <div slot-scope="{ setQuery }">
            <category-filter :set-query="setQuery"></category-filter>
        </div>
    </reactive-component>

    @if(Route::currentRouteName() == 'search')
        <reactive-component component-id="searchterm">
            <div slot-scope="{ setQuery }">
                <search-term-filter :set-query="setQuery" term="{{ request('q') }}"></search-term-filter>
            </div>
        </reactive-component>
    @endif

    <template v-for="filter in filters">
        @include('rapidez::category.partials.filter.price')
        @include('rapidez::category.partials.filter.swatch')
        @include('rapidez::category.partials.filter.boolean')
        @include('rapidez::category.partials.filter.select')
    </template>

    <button type="button" class="md:hidden btn btn-primary w-full" v-on:click="toggle">@lang('Show results')</button>
</x-rapidez::slideover>
