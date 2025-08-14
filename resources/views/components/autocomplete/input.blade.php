<form
    id="autocomplete-form"
    method="get"
    action="{{ route('search') }}"
    class="flex relative z-header-autocomplete"
    {{-- Turbo does not understand redirects to external URLs yet --}}
    data-turbo="false"
>
    <x-rapidez::input
        {{ $attributes->merge([
            'type' => 'search',
            'name' => 'q',
            'autocomplete' => 'off',
            'autocorrect' => 'off',
            'autocapitalize' => 'none',
            'spellcheck' => 'false',
            'placeholder' => __('What are you looking for?'),
            'class' => 'text-base h-12 peer',
            'data-testid' => 'autocomplete-input',
        ]) }}
    />
    <button
        v-on:click="refine('')"
        class="absolute right-14 top-1/2 -translate-y-1/2 transition-opacity opacity-100 peer-placeholder-shown:opacity-0"
        type="reset"
        title="@lang('Clear the search query')"
        v-cloak
    >
        <x-heroicon-s-x-mark class="size-7" />
    </button>
    <x-rapidez::button
        class="absolute right-0 top-0 bg-opacity-0 hover:bg-opacity-0 border-none *:peer-placeholder-shown:bg-muted *:peer-placeholder-shown:text"
        type="submit"
        title="@lang('Search')"
    >
        <x-rapidez::autocomplete.magnifying-glass />
    </x-rapidez::button>
</form>
