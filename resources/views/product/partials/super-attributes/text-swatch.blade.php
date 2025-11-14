<div>
    <x-rapidez::label>
        {{ $superAttribute->frontend_label }}
    </x-rapidez::label>

    <ul class="flex flex-wrap gap-x-1.5 gap-y-2 items-center pr-14">
        @foreach ($product->superAttributeValues[$superAttribute->attribute_code] as $option)
            <li>
                <x-rapidez::input.swatch.text
                    type="radio"
                    name="{{ $superAttribute->attribute_code }}"
                    v-model="addToCart.options[{{ $superAttributeId }}]"
                    v-bind:disabled="addToCart.disabledOptions.super_{{ $superAttribute->attribute_code }}.includes({{ $option['value'] }})"
                    :value="$option['value']"
                    :aria-label="$option['label']"
                    :id="$option['label']"
                    required
                >
                    {{ $option['label'] }}
                </x-rapidez::input.swatch.text>
            </li>
        @endforeach
    </ul>
</div>
