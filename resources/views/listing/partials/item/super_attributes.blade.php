<label v-for="(superAttribute, superAttributeId) in item.super_attributes" class="mt-2 block">
    <x-rapidez::label v-bind:for="item.entity_id+'_super_attribute_'+superAttributeId">
        @{{ superAttribute.frontend_label }}
    </x-rapidez::label>
    <x-rapidez::input.select
        v-bind:id="item.entity_id+'_super_attribute_'+superAttributeId"
        v-bind:name="superAttributeId"
        v-model="addToCart.options[superAttributeId]"
        required
    >
        <option disabled selected hidden :value="undefined">
            @lang('Select') @{{ superAttribute.frontend_label.toLowerCase() }}
        </option>
        <option
            v-for="option in addToCart.getOptions(superAttribute.attribute_code)"
            v-text="option.label"
            :value="option.value"
            :disabled="addToCart.disabledOptions['super_'+superAttribute.attribute_code].includes(option.value)"
        />
    </x-rapidez::input.select>
</label>
