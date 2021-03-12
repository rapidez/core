<div class="w-full bg-white rounded hover:border hover:shadow group relative">
    <a :href="product.url_key + product.url_suffix" class="block"><img :src="product.thumbnail.url" :alt="product.name" loading="lazy" class="object-contain rounded-t h-48 w-full mb-3">
        <div class="px-2">
            <div class="hyphens">
                @{{product.name}}
            </div>
            <div class="font-semibold">
                <graphql v-cloak query="query { currency { base_currency_symbol } }">
                    <div class="font-semibold" v-if="data" slot-scope="{data}">
                        @{{ data.currency.base_currency_symbol }}
                        @{{ product.price_range.maximum_price.regular_price.value }}
                    </div>
                </graphql>
            </div>
        </div>
    </a>
</div>
