<x-rapidez::label for="option_{{ $option->option_id }}">
    {{ $option->title }}
</x-rapidez::label>
<x-rapidez::select
    :label="false"
    id="option_{{ $option->option_id }}"
    multiple
    :required="$option->is_require"
    v-model="addToCart.customSelectedOptions[{{ $option->option_id }}]"
>
    @foreach ($option->values as $value)
        <option value="{{ $value->option_type_id }}">
            {{ $value->title }} {{ $value->price_label }}
        </option>
    @endforeach
</x-rapidez::select>
