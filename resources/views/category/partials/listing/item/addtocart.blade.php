<add-to-cart v-bind:product="item" v-cloak>
    <div class="px-2" slot-scope="{ options, error, add, disabledOptions, price, getValuesByCode }">
        <div class="font-semibold">@{{ price | price}}</div>
        <div v-for="(superAttribute, superAttributeId) in item.super_attributes">
            <x-rapidez::label v-bind:for="'super_attribute_'+superAttributeId">@{{ superAttribute.label }}</x-rapidez::label>
            <x-rapidez::select
                v-bind:id="'super_attribute_'+superAttributeId"
                v-bind:name="superAttributeId"
                v-model="options[superAttributeId]"
                class="block w-full mb-3"
            >
                <option disabled selected hidden :value="undefined">@lang('Select') @{{ superAttribute.label.toLowerCase() }}</option>
                <option
                    v-for="(label, value) in getValuesByCode(superAttribute.code)"
                    v-text="label"
                    :value="value"
                    :disabled="disabledOptions[superAttribute.code].includes(value)"
                />
            </x-rapidez::select>
        </div>

        <div class="py-3">
            <button
                class="btn btn-primary"
                :disabled="$root.loading"
                v-on:click="add"
                dusk="add-to-cart"
            >
                @lang('Add to cart')
            </button>
        </div>

        <div v-if="error" v-text="error" class="text-red-600"></div>
    </div>
</add-to-cart>
