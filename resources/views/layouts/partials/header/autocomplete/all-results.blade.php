<div class="border-b font-sans py-4">
    <a
        :href="'/search?q=' + value"
        class="relative group flex items-center gap-x-4 w-full text-neutral py-2 text-sm"
    >
        @include('rapidez::layouts.partials.header.autocomplete.background')
        <x-heroicon-o-magnifying-glass class="size-5 text-inactive" />
        <span>@lang('View all results for:') <span v-text="value"></span></span>
    </a>
</div>