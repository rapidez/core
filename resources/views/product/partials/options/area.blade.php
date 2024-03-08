<x-rapidez::label for="option_{{ $option->option_id }}">
    {{ $option->title }} {{ $option->price_label }}
</x-rapidez::label>
<x-rapidez::textarea
    :label="false"
    :name="false"
    id="option_{{ $option->option_id }}"
    :required="$option->is_require"
    :maxlength="$option->max_characters ?: false"
    v-model="addToCart.customOptions[{{ $option->option_id }}]"
/>
