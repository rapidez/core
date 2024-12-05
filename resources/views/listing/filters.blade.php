@php($id = uniqid('filters-'))
<x-rapidez::slideover.mobile :$id :title="__('Filters')" position="right">
    <div class="w-full p-2 max-lg:bg-white max-lg:p-5">
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
        <x-rapidez::button.primary for="{{ $id }}" class="w-full text-sm lg:hidden">
            @lang('Show results')
        </x-rapidez::button.primary>
    </div>
</x-rapidez::slideover.mobile>

<x-rapidez::button.secondary :for="$id" class="mb-3 w-full lg:hidden">
    @lang('Filters')
</x-rapidez::button.secondary>
