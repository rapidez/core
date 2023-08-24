<x-rapidez::slideover :mobile-only="true">
    <x-slot:button>
        <x-rapidez::button v-on:click="toggle" class="mb-3 w-full md:hidden">
            @lang('Filters')
        </x-rapidez::button>
    </x-slot:button>
    <x-slot:title>
        @lang('Filters')
    </x-slot:title>
    {{-- On mobile the filters aren't immedately visible so we should defer loading --}}
    <lazy>
        @include('rapidez::listing.partials.filter.selected')
        @include('rapidez::listing.partials.filter.category')
        <template v-for="filter in filters">
            @include('rapidez::listing.partials.filter.price')
            @include('rapidez::listing.partials.filter.swatch')
            @include('rapidez::listing.partials.filter.boolean')
            @include('rapidez::listing.partials.filter.select')
        </template>
    </lazy>
    <x-rapidez::button
        v-on:click="toggle" class="w-full text-sm md:hidden">
        @lang('Show results')
    </x-rapidez::button>
</x-rapidez::slideover>
