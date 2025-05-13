<div class="flex gap-1 sm:items-center text-sm">
    <div class="flex-1">
        @include('rapidez::listing.partials.toolbar.stats')
    </div>
    <label class="flex items-center gap-x-1.5">
        @include('rapidez::listing.partials.toolbar.pages')
    </label>
    <div class="max-md:hidden">
        @include('rapidez::listing.partials.toolbar.sorting')
    </div>
</div>
