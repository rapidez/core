<add-to-cart v-bind:product="item" v-cloak>
    <div class="px-2 pb-2" slot-scope="{ options, error, add, disabledOptions, simpleProduct, getOptions, adding, added }">
        <price :product="simpleProduct" :tax="simpleProduct.tax">
            <div class="flex items-center space-x-2 mb-2" slot-scope="{ price, specialPrice }">
                <div class="font-semibold">@{{ (specialPrice || price) | price }}</div>
                <div class="line-through text-sm" v-if="specialPrice">@{{ price | price }}</div>
            </div>
        </price>

        <p v-if="!item.in_stock" class="text-red-600 text-xs">@lang('Sorry! This product is currently out of stock.')</p>
        <div v-else>
            <div v-for="(superAttribute, superAttributeId) in item.super_attributes">
                <x-rapidez::label v-bind:for="item.id+'_super_attribute_'+superAttributeId">@{{ superAttribute.label }}</x-rapidez::label>
                <x-rapidez::select
                    label=""
                    v-bind:id="item.id+'_super_attribute_'+superAttributeId"
                    v-bind:name="superAttributeId"
                    v-model="options[superAttributeId]"
                    class="block mb-3"
                >
                    <option disabled selected hidden :value="undefined">@lang('Select') @{{ superAttribute.label.toLowerCase() }}</option>
                    <option
                        v-for="(option, optionId) in getOptions(superAttribute.code)"
                        v-text="option.label"
                        :value="optionId"
                        :disabled="disabledOptions['super_'+superAttribute.code].includes(optionId)"
                    />
                </x-rapidez::select>
            </div>

            <x-rapidez::button class="flex items-center" v-on:click="add" dusk="add-to-cart">
                <x-heroicon-o-shopping-cart class="h-5 w-5 mr-2" v-if="!adding && !added" />
                <x-heroicon-o-refresh class="h-5 w-5 mr-2 animate-spin" v-if="adding" />
                <x-heroicon-o-check class="h-5 w-5 mr-2" v-if="added" />
                <span v-if="!adding && !added">@lang('Add to cart')</span>
                <span v-if="adding">@lang('Adding')...</span>
                <span v-if="added">@lang('Added')</span>
            </x-rapidez::button>
        </div>
    </div>
</add-to-cart>
