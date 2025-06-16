<div>
    <x-rapidez::label>
        @{{ superAttribute.label }}
    </x-rapidez::label>

    <ul class="flex flex-wrap gap-x-1.5 gap-y-2 items-center pr-14">
        <li v-for="(option, optionId) in addToCart.getOptions(superAttribute.code)">
            <x-rapidez::input.swatch.visual
                type="radio"
                v-bind:name="superAttribute.code"
                v-model="addToCart.options[superAttributeId]"
                v-bind:value="option.value"
                v-bind:disabled="addToCart.disabledOptions['super_' + superAttribute.code].includes(option.value)"
                v-bind:aria-label="option.label"
                v-bind:id="option.label"
                color="window.config.swatches[superAttribute.code]?.options?.[option.value]?.swatch ?? 'none'"
                required
            >
                @{{ option.label }}
            </x-rapidez::input.swatch.visual>
        </li>
    </ul>
</div>
