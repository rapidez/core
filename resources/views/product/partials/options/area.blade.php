<label>
    <x-rapidez::label>
        {{ $option->title }} {{ $option->price_label }}
    </x-rapidez::label>
    <x-rapidez::input.textarea
        id="option_{{ $option->option_id }}"
        :required="$option->is_require"
        :maxlength="$option->max_characters ?: false"
        v-model="addToCart.customOptions[{{ $option->option_id }}]"
    />
</label>
