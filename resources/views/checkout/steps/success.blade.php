<checkout-success>
    <template slot-scope="{ order, hideBilling, shipping, billing, items }">
        @{{ order || 'No Order' }}
        <div dusk="checkout-success" v-cloak v-if="order">
            <h1 class="font-bold text-4xl mb-5">@lang('Order placed succesfully')</h1>
            <div class="bg-highlight rounded p-8">
                <p>@lang('We will get to work for you right away')</p>
                <p>@lang('We will send a confirmation of your order to') <span class="font-bold">@{{ order.customer_email }}</span></p>
            </div>
            <div class="mt-4">
                <div
                    class="flex flex-wrap items-center mb-4 border-b pb-2"
                    v-for="(item, productId, index) in items"
                >
                    <div class="w-1/6 sm:w-1/12 pr-3">
                        <a :href="item.url" class="block">
                            <img
                                :alt="item.name"
                                :src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/sku/' + item.sku + '.webp'"
                                height="100"
                                class="mx-auto"
                            />
                        </a>
                    </div>
                    <div class="w-5/6 sm:w-5/12 lg:w-5/12">
                        <a :href="item.url" dusk="cart-item-name" class="font-bold">@{{ item.name }}</a>
                        <template v-if="item.product_options?.attributes_info">
                            <div v-for="option in item.product_options.attributes_info">
                                @{{ option.label }}: @{{ option.value }}
                            </div>
                        </template>
                    </div>
                    <div class="w-2/6 sm:w-1/6 lg:w-2/12 text-right pr-5">
                        @{{ Math.round(item.qty_ordered) }}
                    </div>
                    <div class="w-2/6 sm:w-1/6 lg:w-2/12 text-right pr-5">
                        @{{ item.price | price }}
                    </div>
                    <div class="w-2/6 sm:w-1/6 lg:w-2/12 flex justify-end items-center text-right">
                        @{{ item.row_total | price }}
                    </div>
                </div>
            </div>
            <div class="flex flex-col mt-4 gap-x-4 md:flex-row">
                <div class="w-full p-8 bg-highlight rounded border-l-2 border-neutral md:w-1/2" v-if="billing">
                    <p class="text-neutral font-lg font-bold mb-2">@lang('Billing address')</p>
                    <ul>
                        <li>@{{ billing.firstname }} @{{ billing.lastname }}</li>
                        <li>@{{ billing.street }}</li>
                        <li>@{{ billing.postcode }} - @{{ billing.city }} - @{{ billing.country_id }}</li>
                        <li>@{{ billing.telephone }}</li>
                    </ul>
                </div>
                <div class="w-full p-8 bg-highlight rounded border-l-2 border-neutral mt-4 md:mt-0 md:w-1/2" v-if="shipping">
                    <p class="text-neutral font-lg font-bold mb-2">@lang('Shipping address')</p>
                    <ul>
                        <li>@{{ shipping.firstname }} @{{ shipping.lastname }}</li>
                        <li>@{{ shipping.street }}</li>
                        <li>@{{ shipping.postcode }} - @{{ shipping.city }} - @{{ shipping.country_id }}</li>
                        <li>@{{ shipping.telephone }}</li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-col mt-4 gap-x-4 md:flex-row">
                <div class="w-full p-8 bg-highlight rounded border-l-2 border-neutral md:w-1/2">
                    <p class="text-neutral font-lg font-bold mb-2">@lang('Shipping method')</p>
                    <p>@{{ order.shipping_description }}</p>
                </div>
                <div class="w-full p-8 bg-highlight rounded border-l-2 border-neutral mt-4 md:mt-0 md:w-1/2">
                    <p class="text-neutral font-lg font-bold mb-2">@lang('Payment method')</p>
                    <p v-for="method in order.sales_order_payments">@{{ method.additional_information.method_title || method.additional_information.raw_details_info.method_title }}</p>
                </div>
            </div>
        </div>
    </template>
</checkout-success>
