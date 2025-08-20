<label>
    <x-rapidez::label>
        {{ $superAttribute->label }}
    </x-rapidez::label>
    <x-rapidez::input.select
        id="super_attribute_{{ $superAttributeId }}"
        name="{{ $superAttributeId }}"
        v-model="addToCart.options[{{ $superAttributeId }}]"
        class="w-64"
        required
    >
        <option disabled selected hidden :value="undefined">
            @lang('Select') {{ strtolower($superAttribute->label) }}
        </option>
        @foreach($product->{'super_' . $superAttribute->code} as $optionId => $option)
            <option
                value="{{ $option->value }}"
                :disabled="addToCart.disabledOptions.super_{{ $superAttribute->code }}.includes({{ $option->value }})"
            >
                {{ $option->label }}
            </option>
        @endforeach
    </x-rapidez::input.select>
</label>
