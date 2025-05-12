<div class="flex gap-1 sm:items-center text-sm max-sm:flex-col-reverse">
    <div class="flex-1">
        @include('rapidez::listing.partials.toolbar.stats')
    </div>
    <label class="flex items-center gap-x-1.5">
        <x-rapidez::label class="whitespace-nowrap mb-0">
            @lang('Items per page'):
        </x-rapidez::label>
        @include('rapidez::listing.partials.toolbar.pages')
    </label>
    <div class="max-md:hidden">
        @include('rapidez::listing.partials.toolbar.sorting')
    </div>
</div>
