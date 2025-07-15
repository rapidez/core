<label>
    <x-rapidez::label>
        @{{ superAttribute.label }}
    </x-rapidez::label>
    <x-rapidez::input.select
        v-bind:name="superAttributeId"
        v-model="addToCart.options[superAttributeId]"
        required
    >
        <option disabled selected hidden v-bind:value="undefined">
            @lang('Select') @{{ superAttribute.label.toLowerCase() }}
        </option>
        <option
            v-for="(option, optionId) in addToCart.getOptions(superAttribute.code)"
            v-text="option.label"
            v-bind:value="optionId"
            v-bind:disabled="addToCart.disabledOptions['super_'+superAttribute.code].includes(optionId)"
        />
    </x-rapidez::input.select>
</label>
