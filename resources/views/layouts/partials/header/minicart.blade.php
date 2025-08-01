<toggler>
    <div class="relative" v-if="hasCart" v-on-click-away="close" slot-scope="{ toggle, close, isOpen }">
        <button class="flex my-1 focus:outline-none" v-on:click="toggle">
            <x-heroicon-o-shopping-cart class="size-6"/>
            <span class="bg-secondary text-secondary-text text-sm font-bold flex items-center justify-center rounded-full size-6 text-center" data-testid="minicart-count" v-cloak>
                <span>@{{ Math.round(cart.total_quantity) }}</span>
            </span>
        </button>
        <div v-if="isOpen" class="absolute right-0 bg-white border shadow rounded-xl p-5 z-header-minicart" v-cloak>
            <table class="w-full mb-3 *:block *:max-h-96 *:overflow-y-auto *:scrollbar-hide">
                <tr v-for="item in cart.items" class="*:pb-3">
                    <td class="block w-48 truncate overflow-hidden">
                        @{{ item.product.name }}
                        <div class="text-red-600" v-if="!item.is_available">
                            @lang('Out of stock')
                        </div>
                    </td>
                    <td class="text-right px-4">@{{ item.quantity }}</td>
                    <td class="text-right">@{{ item.prices.row_total.value | price }}</td>
                </tr>
                <template v-if="cart.shipping_addresses?.length">
                    <tr v-for="address in cart.shipping_addresses" v-if="address.selected_shipping_method">
                        <td colspan="2">@lang('Shipping')</td>
                        <td class="text-right" v-if="showTax">@{{ address.selected_shipping_method.price_incl_tax.value | price }}</td>
                        <td class="text-right" v-else>@{{ address.selected_shipping_method.price_excl_tax.value | price }}</td>
                    </tr>
                </template>
                <tr class="font-bold *:pt-3 border-t">
                    <td colspan="2">@lang('Total')</td>
                    <td class="text-right">@{{ cart.prices.grand_total.value | price }}</td>
                </tr>
            </table>
            <div class="flex flex-col gap-y-2 items-center">
                <div class="flex w-full" :class="{ 'cursor-not-allowed': !canOrder }">
                    <x-rapidez::button.conversion href="{{ route('checkout') }}" v-bind:class="{ 'pointer-events-none': !canOrder }" class="flex-1">
                        @lang('Checkout')
                    </x-rapidez::button.conversion>
                </div>
                <x-rapidez::button.outline href="{{ route('cart') }}" class="w-full whitespace-nowrap flex-1">
                    @lang('Show cart')
                </x-rapidez::button.outline>
            </div>
        </div>
    </div>
    <a href="{{ route('cart') }}" aria-label="@lang('Cart')" class="my-1" v-else v-cloak>
        <x-heroicon-o-shopping-cart class="size-6"/>
    </a>
</toggler>
