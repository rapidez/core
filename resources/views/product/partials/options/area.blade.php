<x-rapidez::input-field.textarea
    label="{{ $option->title }} {{ $option->price_label }}"
    name="option_{{ $option->option_id }}"
    :required="$option->is_require"
    :maxlength="$option->max_characters ?: false"
    v-model="customOptions[{{ $option->option_id }}]"
/>
