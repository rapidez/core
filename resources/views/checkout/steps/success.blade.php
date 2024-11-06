<checkout-success>
    <template slot-scope="{ order, refreshOrder, hideBilling, shipping, billing, items }">
        <div v-if="order" dusk="checkout-success" class="container" v-cloak>
            <h1 class="font-bold text-4xl mb-5">@lang('Order placed succesfully')</h1>
            <div class="bg-highlight rounded p-8">
                <p>@lang('We will get to work for you right away')</p>
                <p>@lang('We will send a confirmation of your order to') <span class="font-bold">@{{ order.email }}</span></p>
                <p>@lang('Your order is currently:') <span class="font-bold">@{{ order.status }}</span> <a class="inline-block" href="#" v-on:click.prevent="(e) => {e.target.classList.add('animate-spin'); refreshOrder().finally(() => e.target.classList.remove('animate-spin'))}">&#8635;</a></p>
            </div>

            <div class="mt-4">
                <div
                    class="flex flex-wrap items-center mb-4 border-b pb-2"
                    v-for="item in order.items"
                >
                    <div class="w-1/6 sm:w-1/12 pr-3">
                        <a :href="'/' + item.product.url_key + item.product.url_suffix | url" target="blank" class="block">
                            <img
                                :alt="item.product_name"
                                :src="'/storage/{{ config('rapidez.store') }}/resizes/200/sku/' + item.product_sku + '.webp'"
                                height="100"
                                class="mx-auto"
                            />
                        </a>
                    </div>
                    <div class="w-5/6 sm:w-5/12">
                        <a :href="'/' + item.product.url_key + item.product.url_suffix | url" target="blank" dusk="cart-item-name" class="font-bold">@{{ item.product_name }}</a>
                        <div v-for="option in item.selected_options">
                            @{{ option.label }}: @{{ option.value }}
                        </div>
                        <div v-for="option in item.entered_options">
                            @{{ option.label }}: @{{ option.value }}
                        </div>
                    </div>
                    <div class="w-2/6 sm:w-1/6 lg:w-2/12 text-right pr-5">
                        @{{ Math.round(item.quantity_ordered) }}
                    </div>
                    <div class="w-2/6 sm:w-1/6 lg:w-2/12 text-right pr-5">
                        @{{ item.product_sale_price.value | price }}
                    </div>
                    <div class="w-2/6 sm:w-1/6 lg:w-2/12 flex justify-end items-center text-right">
                        @{{ item.product_sale_price.value * item.quantity_ordered | price }}
                    </div>
                </div>
            </div>
            <div class="flex flex-col mt-4 gap-x-4 md:flex-row">
                <div class="w-full p-8 bg-highlight rounded border-l-2 border-neutral md:w-1/2" v-if="order.billing_address">
                    <p class="text-neutral font-lg font-bold mb-2">@lang('Billing address')</p>
                    <ul>
                        <li>@{{ order.billing_address.firstname }} @{{ order.billing_address.lastname }}</li>
                        <li>@{{ order.billing_address.street?.join(' ') }}</li>
                        <li>@{{ order.billing_address.postcode }} - @{{ order.billing_address.city }} - @{{ order.billing_address.country_code }}</li>
                        <li>@{{ order.billing_address.telephone }}</li>
                    </ul>
                </div>
                <div class="w-full p-8 bg-highlight rounded border-l-2 border-neutral mt-4 md:mt-0 md:w-1/2" v-if="order.shipping_address">
                    <p class="text-neutral font-lg font-bold mb-2">@lang('Shipping address')</p>
                    <ul>
                        <li>@{{ order.shipping_address.firstname }} @{{ order.shipping_address.lastname }}</li>
                        <li>@{{ order.shipping_address.street?.join(' ') }}</li>
                        <li>@{{ order.shipping_address.postcode }} - @{{ order.shipping_address.city }} - @{{ order.shipping_address.country_code }}</li>
                        <li>@{{ order.shipping_address.telephone }}</li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-col mt-4 gap-x-4 md:flex-row">
                <div class="w-full p-8 bg-highlight rounded border-l-2 border-neutral md:w-1/2" v-if="order.shipping_method">
                    <p class="text-neutral font-lg font-bold mb-2">@lang('Shipping method')</p>
                    <p>@{{ order.shipping_method }}</p>
                </div>
                <div class="w-full p-8 bg-highlight rounded border-l-2 border-neutral mt-4 md:mt-0 md:w-1/2">
                    <p class="text-neutral font-lg font-bold mb-2">@lang('Payment method')</p>
                    <p v-for="method in order.payment_methods">@{{ method.name || method.type }}</p>
                </div>
            </div>
        </div>
    </template>
</checkout-success>
