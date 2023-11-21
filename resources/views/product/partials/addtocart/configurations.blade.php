<div v-cloak v-for="(superAttribute, superAttributeId) in config.product.super_attributes">
    <x-rapidez::label v-bind:for="'super_attribute_'+superAttributeId">@{{ superAttribute.label }}</x-rapidez::label>
    <x-rapidez::select required label="" v-bind:id="'super_attribute_'+superAttributeId" v-bind:name="superAttributeId" v-model="options[superAttributeId]" class="mb-3 block w-64">
        <option disabled selected hidden :value="undefined">@lang('Select') @{{ superAttribute.label.toLowerCase() }}</option>
        <option
            v-for="option in Object.values(config.product['super_'+superAttribute.code]).sort((a, b) => a.sort_order - b.sort_order)"
            v-text="option.label"
            :value="option.value"
            :disabled="disabledOptions['super_'+superAttribute.code].includes(option.value)"
        />
    </x-rapidez::select>
</div>
