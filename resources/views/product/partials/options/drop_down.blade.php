<x-rapidez::input-field.select
    :label="$option->title"
    name="option_{{ $option->option_id }}"
    dusk="option_{{ $option->option_id }}"
    :required="$option->is_require"
    v-model="customOptions[{{ $option->option_id }}]"
>
    <x-slot:input>
        <option disabled selected hidden :value="undefined">@lang('Select')</option>
        @foreach ($option->values as $value)
            <option value="{{ $value->option_type_id }}">
                {{ $value->title }} {{ $value->price_label }}
            </option>
        @endforeach
    </x-slot:input>
</x-rapidez::input-field.select>
