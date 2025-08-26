<div>
    <x-rapidez::label>
        {{ $superAttribute->label }}
    </x-rapidez::label>

    <ul class="flex flex-wrap gap-x-1.5 gap-y-2 items-center pr-14">
        <li v-for="option in Object.values(config.product.super_{{ $superAttribute->code }}).sort((a, b) => a.label.localeCompare(b.label)).sort((a, b) => a.sort_order - b.sort_order)">
            <x-rapidez::input.swatch.text
                type="radio"
                name="{{ $superAttribute->code }}"
                v-model="addToCart.options[{{ $superAttributeId }}]"
                v-bind:value="option.value"
                v-bind:disabled="addToCart.disabledOptions.super_{{ $superAttribute->code }}.includes(option.value)"
                v-bind:aria-label="option.label"
                v-bind:id="option.label"
                required
            >
                @{{ option.label }}
            </x-rapidez::input.swatch.text>
        </li>
    </ul>
</div>
