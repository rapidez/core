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
        <option
            v-for="option in Object.values(config.product.super_{{ $superAttribute->code }}).sort((a, b) => a.label.localeCompare(b.label)).sort((a, b) => a.sort_order - b.sort_order)"
            v-text="option.label"
            :value="option.value"
            :disabled="addToCart.disabledOptions.super_{{ $superAttribute->code }}.includes(option.value)"
        />
    </x-rapidez::input.select>
</label>
