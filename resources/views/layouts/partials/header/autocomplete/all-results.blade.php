<div class="font-sans px-5">
    <x-rapidez::button.primary
        v-bind:href="'/search?q=' + dataSearchScope.value"
        class="relative group flex items-center gap-x-4 w-full text-sm"
    >
        <span>@lang('View all products')</span>
    </x-rapidez::button.primary>
</div>
