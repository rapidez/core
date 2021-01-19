<add-to-cart v-cloak>
    <div slot-scope="{ qty, changeQty, options, error, add, disabledOptions }">
        <div v-for="(superAttribute, superAttributeId) in config.product.super_attributes">
            <label :for="'super_attribute_'+superAttributeId">@{{ superAttribute.label }}</label>
            <div class="relative w-64 mb-3">
                <select
                    :id="'super_attribute_'+superAttributeId"
                    :name="superAttributeId"
                    v-model="options[superAttributeId]"
                    class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline"
                >
                    <option disabled selected hidden :value="undefined">@lang('Select') @{{ superAttribute.label.toLowerCase() }}</option>
                    <option
                        v-for="(label, value) in config.product[superAttribute.code]"
                        v-text="label"
                        :value="value"
                        :disabled="disabledOptions[superAttribute.code].includes(value)"
                    />
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                </div>
            </div>
        </div>

        <div class="flex">
            <select :value="qty" v-on:input="changeQty" class="border rounded p-2 mr-2">
                @for ($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>

            <button
                class="block btn btn-primary"
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
