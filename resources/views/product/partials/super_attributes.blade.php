@foreach($product->super_attributes ?: [] as $superAttributeId => $superAttribute)
    <div>
        <x-rapidez::label for="super_attribute_{{ $superAttributeId }}">
            {{ $superAttribute->label }}
        </x-rapidez::label>
        <x-rapidez::select
            id="super_attribute_{{ $superAttributeId }}"
            name="{{ $superAttributeId }}"
            label=""
            v-model="options[{{ $superAttributeId }}]"
            class="!w-64"
            required
        >
            <option disabled selected hidden :value="undefined">
                @lang('Select') {{ strtolower($superAttribute->label) }}
            </option>
            <option
                v-for="option in Object.values(config.product.super_{{ $superAttribute->code }}).sort((a, b) => a.sort_order - b.sort_order)"
                v-text="option.label"
                :value="option.value"
                :disabled="disabledOptions.super_{{ $superAttribute->code }}.includes(option.value)"
            />
        </x-rapidez::select>
    </div>
@endforeach
