@props(['id' => 'autocomplete'])

<form name="{{ $id }}-form" id="{{ $id }}-form" method="get" action="{{ route('search') }}" class="flex relative z-header-autocomplete">
    <x-rapidez::input
        {{ $attributes->merge([
            'id' => $id . '-input',
            'type' => 'search',
            'name' => 'q',
            'autocomplete' => 'off',
            'autocorrect' => 'off',
            'autocapitalize' => 'none',
            'spellcheck' => 'false',
            'placeholder' => __('What are you looking for?'),
            'class' => 'h-12 peer',
        ]) }}
    />
    <x-rapidez::button
        v-on:click="refine('')"
        class="absolute right-10 bg-opacity-0 hover:bg-opacity-0 border-none transition-all opacity-100 peer-placeholder-shown:opacity-0"
        type="reset"
        title="__('Clear the search query')"
        v-cloak
    >
        <x-heroicon-s-x-mark class="size-7" />
    </x-rapidez::button>
    <x-rapidez::button
        class="absolute right-0 bg-opacity-0 hover:bg-opacity-0 border-none *:peer-placeholder-shown:bg-muted *:peer-placeholder-shown:text"
        type="submit"
    >
        <x-rapidez::autocomplete.magnifying-glass />
    </x-rapidez::button>
</form>
