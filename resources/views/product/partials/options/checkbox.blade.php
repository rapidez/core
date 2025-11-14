<span>
    <x-rapidez::label>
        {{ $option->title }}
    </x-rapidez::label>
    <div class="flex flex-col gap-2">
        @foreach ($option->values as $value)
            <x-rapidez::input.checkbox
                value="{{ $value->option_type_id }}"
                :required="$option->is_require"
                name="option_{{ $option->option_id }}[]"
                v-model="addToCart.customSelectedOptions[{{ $option->option_id }}]"
                v-on:input="(event) => addToCart.customSelectedOptions[{{ $option->option_id }}] === undefined ? (addToCart.customSelectedOptions[{{ $option->option_id }}] = event.target.checked ? [event.target.value] : []) : ''"
            >
                {{ $value->title }} {{ $value->price_label }}
            </x-rapidez::input.checkbox>
        @endforeach
    </div>
</span>
