<div v-for="(superAttribute, superAttributeId) in item.super_attributes">
    <div v-if="superAttribute.visual_swatch">
        <div class="relative" v-for="(option, optionId) in addToCart.getOptions(superAttribute.code)">
            <label
                v-bind:for="item.entity_id+'_super_attribute_'+superAttributeId + '_option_' + optionId"
                class="relative"
            >
                <div
                    v-if="option.type === 1"
                    class="w-6 h-6 border-black mr-1 mb-1 rounded-full hover:opacity-75"
                    :style="{ 'background': option.swatch }"
                ></div>
                <img class="w-6 h-6 border-black mr-1 mb-1 rounded-full" v-if="option.type === 2" v-bind:src="'/storage/'+config.store+'/resizes/400/magento/catalog/product'+option.swatch+'.webp'" alt="" />
                <x-rapidez::button.outline v-else-if="option.type === 3">
                    @{{ option.swatch }}
                </x-rapidez::button.outline>
            </label>
            <input
                v-bind:id="item.entity_id+'_super_attribute_'+superAttributeId + '_option_' + optionId"
                class="hidden peer"
                :value="optionId"
                type="radio"
                v-model="addToCart.options[superAttributeId]"
            />
            <x-heroicon-o-check class="hidden peer-checked:block w-6 h-6 absolute top-1/2 transform -translate-y-1/2"/>
        </div>
    </div>
    <div v-else>
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
</div>
