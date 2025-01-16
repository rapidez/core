{{-- TODO: Double check the responsiveness --}}
<div class="flex gap-1 items-center text-sm">
    <div class="flex-1">
        @include('rapidez::listing.partials.toolbar.stats')
    </div>
    <label class="flex items-center gap-x-1.5">
        <x-rapidez::label class="whitespace-nowrap mb-0">
            @lang('Items per page'):
        </x-rapidez::label>
        @include('rapidez::listing.partials.toolbar.pages')
    </label>
    @include('rapidez::listing.partials.toolbar.sorting')
</div>
