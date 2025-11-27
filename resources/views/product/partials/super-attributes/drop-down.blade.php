<label>
    <x-rapidez::label>
        {{ $superAttribute->frontend_label }}
    </x-rapidez::label>
    <x-rapidez::input.select
        id="super_attribute_{{ $superAttributeId }}"
        name="{{ $superAttributeId }}"
        v-model="addToCart.options[{{ $superAttributeId }}]"
        class="w-64"
        required
    >
        <option disabled selected hidden :value="undefined">
            @lang('Select') {{ strtolower($superAttribute->frontend_label) }}
        </option>
        @foreach ($product->superAttributeValues[$superAttribute->attribute_code] as $option)
            <option
                value="{{ $option->value }}"
                :disabled="addToCart.disabledOptions.super_{{ $superAttribute->attribute_code }}.includes({{ $option->value }})"
            >
                {{ $option->label }}
            </option>
        @endforeach
    </x-rapidez::input.select>
</label>
