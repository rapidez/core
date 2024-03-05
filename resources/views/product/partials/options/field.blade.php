<x-rapidez::input-field
    label="{{ $option->title }} {{ $option->price_label }}"
    :required="$option->is_require"
    :maxlength="$option->max_characters ?: false"
    v-model="customOptions[{{ $option->option_id }}]"
/>

