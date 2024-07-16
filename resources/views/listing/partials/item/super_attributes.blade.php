<template v-for="(superAttribute, superAttributeId) in item.super_attributes">
    <label>
        <x-rapidez::input.label>@{{ superAttribute.label }}</x-rapidez::input.label>
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
                v-for="(option, optionId) in addToCart.getOptions(superAttribute.code)"
                v-text="option.label"
                :value="optionId"
                :disabled="addToCart.disabledOptions['super_'+superAttribute.code].includes(optionId)"
            />
        </x-rapidez::select>
    </label>
</template>
