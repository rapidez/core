{{-- This is the search icon in the autocomplete --}}
<div {{ $attributes->twMerge('transition-colors duration-300 rounded-lg bg-inactive-100 absolute right-1 inset-y-1 w-11 pointer-events-none flex items-center justify-center text-neutral ' . config('rapidez.frontend.z-indexes.button-autocomplete')) }}>
    <x-heroicon-o-magnifying-glass class="size-5" />
</div>
