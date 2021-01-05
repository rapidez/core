<x-rapidez::slideover :mobile-only="true">
    <x-slot name="button">
        <button type="button" class="md:hidden btn btn-primary w-full mb-3" v-on:click="toggle">@lang('Filters')</button>
    </x-slot>

    <x-slot name="title">
        @lang('Filters')
    </x-slot>

    <reactive-component component-id="category">
        <div slot-scope="{ setQuery }">
            <category-filter :set-query="setQuery"></category-filter>
        </div>
    </reactive-component>

    <template v-for="filter in filters">
        @include('rapidez::category.partials.filter.price')
        @include('rapidez::category.partials.filter.swatch')
        @include('rapidez::category.partials.filter.boolean')
        @include('rapidez::category.partials.filter.select')
    </template>
</x-rapidez::slideover>
