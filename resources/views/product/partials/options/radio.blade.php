<span>
    <x-rapidez::label>
        {{ $option->title }}
    </x-rapidez::label>
    <div class="flex flex-col gap-2">
        @if (!$option->is_require)
            <x-rapidez::input.radio v-bind:value="undefined" name="option_{{ $option->option_id }}" v-model="addToCart.customSelectedOptions[{{ $option->option_id }}]">
                @lang('No selection')
            </x-rapidez::input.radio>
        @endif
        @foreach ($option->values as $value)
            <x-rapidez::input.radio value="{{ $value->option_type_id }}" :required="$option->is_require" name="option_{{ $option->option_id }}" v-model="addToCart.customSelectedOptions[{{ $option->option_id }}]">
                {{ $value->title }} {{ $value->price_label }}
            </x-rapidez::input.radio>
        @endforeach
    </div>
</span>
