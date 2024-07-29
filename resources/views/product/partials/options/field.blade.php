<x-rapidez::input
    :required="$option->is_require"
    :maxlength="$option->max_characters ?: false"
    v-model="addToCart.customOptions[{{ $option->option_id }}]"
>
    {{ $option->title }} {{ $option->price_label }}
</x-rapidez::input>
