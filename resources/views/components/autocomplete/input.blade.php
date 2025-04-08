@props([
    'id' => 'autocomplete'
])

<form name="{{ $id }}-form" id="{{ $id }}-form" method="get" action="{{ route('search') }}" class="flex flex-row relative">
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
        ]) }}
        v-bind:value="currentRefinement"
        v-on:focus="() => {
            refine($root.autocompleteFacadeQuery || currentRefinement);
            $root.autocompleteFacadeQuery = null;
        }"
        v-on:input="refine($event.currentTarget.value)"
    />
    <x-rapidez::button class="absolute right-0 bg-opacity-0 hover:bg-opacity-0 border-none" type="submit">
        <x-rapidez::autocomplete.magnifying-glass />
    </x-rapidez::button>
</form>
