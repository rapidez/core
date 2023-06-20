<x-rapidez::slideover :mobile-only="true">
    <x-slot:button>
        <x-rapidez::button
            class="mb-3 w-full md:hidden"
            v-on:click="toggle"
        >
            @lang('Filters')
        </x-rapidez::button>
    </x-slot:button>
    <x-slot:title>
        @lang('Filters')
    </x-slot:title>
    {{-- On mobile the filters aren't immedately visible so we should defer loading --}}
    <lazy>
        <template>
            <div class="lg:pr-4">
                @include('rapidez::listing.partials.filter.selected')
                @include('rapidez::listing.partials.filter.category')
                <template v-for="filter in filters">
                    @include('rapidez::listing.partials.filter.price')
                    @include('rapidez::listing.partials.filter.swatch')
                    @include('rapidez::listing.partials.filter.boolean')
                    @include('rapidez::listing.partials.filter.select')
                </template>
            </div>
        </template>
    </lazy>
    <x-rapidez::button
        class="w-full text-sm md:hidden"
        v-on:click="toggle"
    >
        @lang('Show results')
    </x-rapidez::button>
</x-rapidez::slideover>
