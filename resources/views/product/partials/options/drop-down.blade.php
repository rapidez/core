<label>
    <x-rapidez::label>
        {{ $option->title }}
    </x-rapidez::label>
    <x-rapidez::input.select
        id="option_{{ $option->option_id }}"
        :required="$option->is_require"
        v-model="addToCart.customSelectedOptions[{{ $option->option_id }}]"
    >
        <option
            @if ($option->is_require)
                disabled
                hidden
            @endif
            selected
            :value="undefined"
        >
            @if ($option->is_require)
                @lang('Select')
            @else
                @lang('No selection')
            @endif
        </option>
        @foreach ($option->values as $value)
            <option value="{{ $value->option_type_id }}">
                {{ $value->title }} {{ $value->price_label }}
            </option>
        @endforeach
    </x-rapidez::input.select>
</label>
