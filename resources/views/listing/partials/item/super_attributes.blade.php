<div v-for="(superAttribute, superAttributeId) in item.super_attributes">
    <x-rapidez::label v-bind:for="item.entity_id+'_super_attribute_'+superAttributeId">
        @{{ superAttribute.label }}
    </x-rapidez::label>
    <x-rapidez::select
        label=""
        v-bind:id="item.entity_id+'_super_attribute_'+superAttributeId"
        v-bind:name="superAttributeId"
        v-model="addToCart.options[superAttributeId]"
        class="block mb-3"
        required
    >
        <option disabled selected hidden :value="undefined">
            @lang('Select') @{{ superAttribute.label.toLowerCase() }}
        </option>
        <option
            v-for="(option, optionId) in addToCart.getOptions(superAttribute.code)"
            v-text="option.label"
            :value="optionId"
            :disabled="addToCart.disabledOptions['super_'+superAttribute.code].includes(optionId)"
        />
    </x-rapidez::select>
</div>
