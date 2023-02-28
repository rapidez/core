<checkout-success v-slot="{ order }">
    <div dusk="checkout-success">
        <h1 class="font-bold text-4xl mb-5">@lang('Order placed succesfully')</h1>
        <div class="bg-highlight rounded p-8">
            <p>@lang('We will get to work for you right away')</p>
            <p>@lang('We will send a confirmation of your order to') <span class="font-bold">@{{ order.email }}</span></p>
        </div>
        <div class="mt-4">
            <div class="flex flex-wrap items-center mb-4 border-b pb-2" v-for="(item, productId, index) in order.items">
                <div class="w-1/6 sm:w-1/12 pr-3">
                    <a :href="item.url" class="block">
                        <img
                            :alt="item.name"
                            :src="'/storage/resizes/80x80/catalog/product' + item.image + '.webp'"
                            height="100"
                            class="mx-auto"
                        />
                    </a>
                </div>
                <div class="w-5/6 sm:w-5/12 lg:w-5/12">
                    <a :href="item.url" dusk="cart-item-name" class="font-bold">@{{ item.name }}</a>
                    <div v-for="(optionValue, option) in item.options">
                        @{{ option }}: @{{ optionValue }}
                    </div>
                </div>
                <div class="w-2/6 sm:w-1/6 lg:w-2/12 text-right pr-5">
                    @{{ item.qty }}
                </div>
                <div class="w-2/6 sm:w-1/6 lg:w-2/12 text-right pr-5">
                    @{{ item.price | price }}
                </div>
                <div class="w-2/6 sm:w-1/6 lg:w-2/12 flex justify-end items-center text-right">
                    @{{ item.total | price }}
                </div>
            </div>
        </div>
        <div class="flex flex-col mt-4 gap-x-4 md:flex-row">
            <div class="w-full p-8 bg-highlight rounded border-l-2 border-primary md:w-1/2">
                <p class="text-primary font-lg font-bold mb-2">@lang('Billing address')</p>
                <ul>
                    <li>@{{ order.billingAddress.firstname }} @{{ order.billingAddress.lastname }}</li>
                    <li>@{{ order.billingAddress.street[0] }} @{{ order.billingAddress.street[1] }} @{{ order.billingAddress.street[2] }}</li>
                    <li>@{{ order.billingAddress.postcode }} - @{{ order.billingAddress.city }} - @{{ order.billingAddress.country_id }}</li>
                    <li>@{{ order.billingAddress.telephone }}</li>
                </ul>
            </div>
            <div class="w-full p-8 bg-highlight rounded border-l-2 border-primary mt-4 md:mt-0 md:w-1/2">
                <p class="text-primary font-lg font-bold mb-2">@lang('Shipping address')</p>
                <ul>
                    <li>@{{ order.shippingAddress.firstname }} @{{ order.shippingAddress.lastname }}</li>
                    <li>@{{ order.shippingAddress.street[0] }} @{{ order.shippingAddress.street[1] }} @{{ order.shippingAddress.street[2] }}</li>
                    <li>@{{ order.shippingAddress.postcode }} - @{{ order.shippingAddress.city }} - @{{ order.shippingAddress.country_id }}</li>
                    <li>@{{ order.shippingAddress.telephone }}</li>
                </ul>
            </div>
        </div>

        <div class="flex flex-col mt-4 gap-x-4 md:flex-row">
            <div class="w-full p-8 bg-highlight rounded border-l-2 border-primary md:w-1/2">
                <p class="text-primary font-lg font-bold mb-2">@lang('Shipping method')</p>
                <p>@{{ order.shippingMethod }}</p>
            </div>
            <div class="w-full p-8 bg-highlight rounded border-l-2 border-primary mt-4 md:mt-0 md:w-1/2">
                <p class="text-primary font-lg font-bold mb-2">@lang('Payment method')</p>
                <p>@{{ order.paymentMethod }}</p>
            </div>
        </div>
    </div>
</checkout-success>
