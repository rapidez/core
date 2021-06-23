<x-rapidez::slideover v-on-click-away="close" :mobile-only="true">
    <x-slot name="button">
        <x-rapidez::button class="md:hidden w-full mb-3" v-on:click="toggle">
            @lang('Filters')
        </x-rapidez::button>
    </x-slot>

    <x-slot name="title">
        @lang('Filters')
    </x-slot>

    @include('rapidez::listing.partials.filter.selected')

    <template v-for="filter in filters">
        @include('rapidez::listing.partials.filter.price')
        @include('rapidez::listing.partials.filter.swatch')
        @include('rapidez::listing.partials.filter.boolean')
        @include('rapidez::listing.partials.filter.select')
    </template>

    <x-rapidez::button class="md:hidden w-full text-sm" v-on:click="toggle">
        @lang('Show results')
    </x-rapidez::button>
</x-rapidez::slideover>
