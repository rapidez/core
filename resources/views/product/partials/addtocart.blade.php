<add-to-cart v-cloak>
    <div slot-scope="{ qty, changeQty, options, error, add, disabledOptions, price }">
        <div class="font-bold text-3xl mb-3">@{{ price | price}}</div>

        @if(!$product->in_stock)
            <p class="text-red-600">@lang('Sorry! This product is currently out of stock.')</p>
        @else
            <div v-for="(superAttribute, superAttributeId) in config.product.super_attributes">
                <x-rapidez::label v-bind:for="'super_attribute_'+superAttributeId">@{{ superAttribute.label }}</x-rapidez::label>
                <x-rapidez::select
                    v-bind:id="'super_attribute_'+superAttributeId"
                    v-bind:name="superAttributeId"
                    v-model="options[superAttributeId]"
                    class="block w-64 mb-3"
                >
                    <option disabled selected hidden :value="undefined">@lang('Select') @{{ superAttribute.label.toLowerCase() }}</option>
                    <option
                        v-for="(label, value) in config.product[superAttribute.code]"
                        v-text="label"
                        :value="value"
                        :disabled="disabledOptions[superAttribute.code].includes(value)"
                    />
                </x-rapidez::select>
            </div>

            <div class="flex items-center mt-5">
                <x-rapidez::label for="qty" class="mr-3 sr-only">@lang('Quantity')</x-rapidez::label>
                <x-rapidez::select id="qty" v-bind:value="qty" v-on:input="changeQty" class="mr-3">
                    @for ($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </x-rapidez::select>

                <button
                    class="btn btn-primary"
                    :disabled="$root.loading"
                    v-on:click="add"
                    dusk="add-to-cart"
                >
                    @lang('Add to cart')
                </button>
            </div>

            <p v-if="error" v-text="error" class="text-red-600"></p>
        @endif
    </div>
</add-to-cart>
