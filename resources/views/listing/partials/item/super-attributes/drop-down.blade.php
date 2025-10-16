<label>
    <x-rapidez::label>
        @{{ superAttribute.frontend_label }}
    </x-rapidez::label>
    <x-rapidez::input.select
        v-bind:name="superAttributeId"
        v-model="addToCart.options[superAttributeId]"
        required
    >
        <option disabled selected hidden v-bind:value="undefined">
            @lang('Select') @{{ superAttribute.frontend_label.toLowerCase() }}
        </option>
        <option
            v-for="(option, optionId) in addToCart.getOptions(superAttribute.attribute_code)"
            v-text="option.label"
            v-bind:value="optionId"
            v-bind:disabled="addToCart.disabledOptions['super_'+superAttribute.attribute_code].includes(optionId)"
        />
    </x-rapidez::input.select>
</label>
