<price :product="simpleProduct" v-if="tierPrices && Object.keys(tierPrices).length" v-cloak>
    <ul class="flex flex-col" slot-scope="{ calculatePrice }">
        <li v-for="tier in tierPrices">
            @lang('Order :amount and pay :price per item', [
                'amount' => '@{{ Math.round(tier.qty) }}',
                'price' => '@{{ calculatePrice({price: tier.price}) | price }}',
            ])
        </li>
    </ul>
</price>
