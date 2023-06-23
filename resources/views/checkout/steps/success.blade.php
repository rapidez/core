<checkout-success>
    <div dusk="checkout-success">
        <h1 class="font-bold text-4xl mb-5">@lang('Order placed succesfully')</h1>
        <div class="bg-highlight rounded p-8">
            <p>@lang('We will get to work for you right away')</p>
            <p>@lang('We will send a confirmation of your order to') <span class="font-bold">@{{$root.user?.email}}</span></p>
        </div>
        <div class="mt-4">
            <div class="flex flex-wrap items-center mb-4 border-b pb-2" v-for="(item, productId, index) in cart.items">
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
            <div class="w-full p-8 bg-highlight rounded border-l-2 border-neutral md:w-1/2">
                <p class="text-neutral font-lg font-bold mb-2">@lang('Billing address')</p>
                <ul>
                    <li>@{{ $root.checkout?.billing_address?.firstname }} @{{ $root.checkout?.billing_address?.lastname }}</li>
                    <li>@{{ $root.checkout?.billing_address?.street[0] }} @{{ $root.checkout?.billing_address?.street[1] }} @{{ $root.checkout?.billing_address?.street[2] }}</li>
                    <li>@{{ $root.checkout?.billing_address?.postcode }} - @{{ $root.checkout?.billing_address?.city }} - @{{ $root.checkout?.billing_address?.country_id }}</li>
                    <li>@{{ $root.checkout?.billing_address?.telephone }}</li>
                </ul>
            </div>
            <div class="w-full p-8 bg-highlight rounded border-l-2 border-neutral mt-4 md:mt-0 md:w-1/2">
                <p class="text-neutral font-lg font-bold mb-2">@lang('Shipping address')</p>
                <ul>
                    <li>@{{ $root.checkout?.shipping_address?.firstname }} @{{ $root.checkout?.shipping_address?.lastname }}</li>
                    <li>@{{ $root.checkout?.shipping_address?.street[0] }} @{{ $root.checkout?.shipping_address?.street[1] }} @{{ $root.checkout?.billing_address?.street[2] }}</li>
                    <li>@{{ $root.checkout?.shipping_address?.postcode }} - @{{ $root.checkout?.shipping_address?.city }} - @{{ $root.checkout?.shipping_address?.country_id }}</li>
                    <li>@{{ $root.checkout?.shipping_address?.telephone }}</li>
                </ul>
            </div>
        </div>

        <div class="flex flex-col mt-4 gap-x-4 md:flex-row">
            <div class="w-full p-8 bg-highlight rounded border-l-2 border-neutral md:w-1/2">
                <p class="text-neutral font-lg font-bold mb-2">@lang('Shipping method')</p>
                <p>@{{ $root.checkout?.shipping_method }}</p>
            </div>
            <div class="w-full p-8 bg-highlight rounded border-l-2 border-neutral mt-4 md:mt-0 md:w-1/2">
                <p class="text-neutral font-lg font-bold mb-2">@lang('Payment method')</p>
                <p>@{{ $root.checkout?.payment_method }}</p>
            </div>
        </div>
    </div>
</checkout-success>
