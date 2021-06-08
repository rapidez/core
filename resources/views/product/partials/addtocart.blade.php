<add-to-cart v-cloak>
    <div slot-scope="{ qty, changeQty, options, error, add, disabledOptions, simpleProduct, swatchClicked }">
        <div class="font-bold text-3xl mb-3">@{{ simpleProduct.price | price }}</div>
        @if(!$product->in_stock)
            <p class="text-red-600">@lang('Sorry! This product is currently out of stock.')</p>
        @else
            <div v-for="(superAttribute, superAttributeId) in config.product.super_attributes">
                <x-rapidez::label v-bind:for="'super_attribute_'+superAttributeId">
                    @{{ superAttribute.label }}
                </x-rapidez::label>
                <div v-if="superAttribute.swatch_type === 'visual'">
                    <div class="flex flex-row flex-wrap">
                        <div v-for="(label, value) in config.product[superAttribute.code]" class="mr-1">
                            <button
                                v-if="label.visual_swatch.includes('#')"
                                :id="superAttributeId + '-' + value"
                                class="relative w-9 h-9 cursor-pointer outline-none focus:outline-none rounded border-2 border-transparent"
                                :style="'background:'+ label.visual_swatch"
                                v-on:click="swatchClicked(value, superAttributeId)"
                                :disabled="disabledOptions[superAttribute.code].includes(value)"
                            >
                                <span v-if="disabledOptions[superAttribute.code].includes(value)" class="absolute h-px w-full left-0 bg-red-600 transform skew-y-45 -skew-x-30"></span>
                            </button>
                            <button
                                v-if="!label.visual_swatch.includes('#')"
                                :id="superAttributeId + '-' + value"
                                class="relative w-9 h-9 cursor-pointer outline-none focus:outline-none rounded border-2 border-transparent"
                                :style="'background:url('+config.magento_url + '/media/attribute/swatch/swatch_image/30x20' + label.visual_swatch +')'"
                                v-on:click="swatchClicked(value, superAttributeId)"
                                :disabled="disabledOptions[superAttribute.code].includes(value)"
                            >
                                <span v-if="disabledOptions[superAttribute.code].includes(value)" class="absolute h-px w-full left-0 bg-red-600 transform skew-y-45 -skew-x-30"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="superAttribute.swatch_type == 'text'">
                    <div class="flex flex-row flex-wrap">
                        <button
                            class="flex justify-center items-center border-2 border-transparent bg-gray-100 outline-none focus:outline-none rounded w-10 h-10 mr-1"
                            v-for="(label, value) in config.product[superAttribute.code]"
                            :id="superAttributeId + '-' + value"
                            v-on:click="swatchClicked(value, superAttributeId)"                             :disabled="disabledOptions[superAttribute.code].includes(value)"
                        >
                            @{{ label.text_swatch }}
                            <span v-if="disabledOptions[superAttribute.code].includes(value)" class="absolute h-px w-[inherit] bg-red-600 transform skew-y-45 -skew-x-30"></span>
                        </button>
                    </div>
                </div>
                <div v-if="superAttribute.swatch_type == null">
                    <x-rapidez::select
                        label=""
                        v-bind:id="'super_attribute_'+superAttributeId"
                        v-bind:name="superAttributeId"
                        v-model="options[superAttributeId]"
                        class="block w-64 mb-3"
                    >
                        <option disabled selected hidden :value="undefined">@lang('Select') @{{ superAttribute.label.toLowerCase() }}</option>
                        <option
                            v-for="(label, value) in config.product[superAttribute.code]"
                            v-text="label.text_swatch"
                            :value="value"
                            :disabled="disabledOptions[superAttribute.code].includes(value)"
                        />
                    </x-rapidez::select>
                </div>
            </div>
            <div class="flex items-center mt-5">
                <x-rapidez::select
                    name="qty"
                    label="Quantity"
                    v-bind:value="qty"
                    v-on:input="changeQty"
                    class="w-auto mr-3"
                    labelClass="mr-3 sr-only"
                >
                    @for ($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </x-rapidez::select>

                <x-rapidez::button v-on:click="add" dusk="add-to-cart">
                    @lang('Add to cart')
                </x-rapidez::button>
            </div>

            <p v-if="error" v-text="error" class="text-red-600"></p>
        @endif
    </div>
</add-to-cart>
