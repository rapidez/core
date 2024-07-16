<label>
    <x-rapidez::input.label>{{ $option->title }}</x-rapidez::input.label>
    <x-rapidez::input.select
        :required="$option->is_require"
        v-model="addToCart.customSelectedOptions[{{ $option->option_id }}]"
    >
        <option disabled selected hidden :value="undefined">@lang('Select')</option>
        @foreach ($option->values as $value)
            <option value="{{ $value->option_type_id }}">
                {{ $value->title }} {{ $value->price_label }}
            </option>
        @endforeach
    </x-rapidez::input.select>
</label>
