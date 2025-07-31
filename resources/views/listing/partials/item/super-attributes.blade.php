<label v-for="(superAttribute, superAttributeId) in item.super_attributes" class="mt-2 block">
    <x-rapidez::label v-bind:for="item.entity_id+'_super_attribute_'+superAttributeId">
        @{{ superAttribute.label }}
    </x-rapidez::label>
    <x-rapidez::input.select
        v-bind:id="item.entity_id+'_super_attribute_'+superAttributeId"
        v-bind:name="superAttributeId"
        v-model="addToCart.options[superAttributeId]"
        required
    >
        <option disabled selected hidden :value="undefined">
            @lang('Select') @{{ superAttribute.label.toLowerCase() }}
        </option>
        <option
            v-for="option in addToCart.getOptions(superAttribute.code)"
            v-text="option.label"
            :value="option.value"
            :disabled="addToCart.disabledOptions['super_'+superAttribute.code].includes(option.value)"
        />
    </x-rapidez::input.select>
</label>
