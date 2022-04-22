<x-rapidez::slideover v-on-click-away="close" :mobile-only="true">
    <x-slot name="button">
        <x-rapidez::button class="md:hidden w-full mb-3" v-on:click="toggle">
            @lang('Filters')
        </x-rapidez::button>
    </x-slot>

    <x-slot name="title">
        @lang('Filters')
    </x-slot>
    {{-- On mobile the filters aren't immedately visible so we should defer loading --}}
    <lazy>
        <template>
            @include('rapidez::listing.partials.filter.selected')
            @include('rapidez::listing.partials.filter.category')

            <template v-for="filter in filters">
                @include('rapidez::listing.partials.filter.price')
                @include('rapidez::listing.partials.filter.swatch')
                @include('rapidez::listing.partials.filter.boolean')
                @include('rapidez::listing.partials.filter.select')
            </template>
        </template>
    </lazy>
    <x-rapidez::button class="md:hidden w-full text-sm" v-on:click="toggle">
        @lang('Show results')
    </x-rapidez::button>
</x-rapidez::slideover>
