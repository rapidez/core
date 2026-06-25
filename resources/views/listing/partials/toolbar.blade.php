<div class="flex gap-1 sm:items-center text-sm">
    <div class="flex-1">
        {{-- TODO: Check this one as it's not visible atm --}}
        @include('rapidez::listing.partials.toolbar.stats')
    </div>
    @include('rapidez::listing.partials.toolbar.pages')
    @include('rapidez::listing.partials.toolbar.sorting')
</div>
