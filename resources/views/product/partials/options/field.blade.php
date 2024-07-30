<x-rapidez::input
    :required="$option->is_require"
    :maxlength="$option->max_characters ?: false"
    v-model="addToCart.customOptions[{{ $option->option_id }}]"
>
    <x-slot:label>{{ $option->title }} {{ $option->price_label }}</x-slot:label>
</x-rapidez::input>
