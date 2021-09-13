<minicart v-cloak v-if="mask" query='@include("rapidez::cart.queries.cart")' :variables='{cart_id: mask}' cache="cart" :after-resolved-data="processCartData">
    <toggler slot-scope="{ data }">
        <div v-if="data && data.cart && data.cart.items && Object.keys(data.cart.items).length" v-on-click-away="close" slot-scope="{toggle, close, isOpen}">
            <button class="flex my-1 focus:outline-none" v-on:click="toggle">
                <x-heroicon-o-shopping-cart class="h-6 w-6"/>
                <span class="bg-primary rounded-full w-6 h-6 text-white text-center">@{{ Math.round(data.cart.total_quantity) }}</span>
            </button>
            <div v-if="isOpen" class="absolute right-0 bg-white border shadow rounded p-3 mr-1 z-10">
                <table class="mb-3">
                    <tr class="py-3" v-for="item in data.cart.items">
                        <td>@{{ item.product.name }}</td>
                        <td class="text-right px-4">@{{ item.quantity }}</td>
                        <td class="text-right">@{{ item.prices.price.value | price }}</td>
                    </tr>
                    <tr class="py-3">
                        <td colspan="2">@lang('Total')</td>
                        <td class="text-right">@{{ cart.prices.grand_total.value | price }}</td>
                    </tr>
                </table>
                <div class="flex justify-between items-center">
                    <x-rapidez::button href="/cart" variant="outline" class="mr-5">
                        @lang('Show cart')
                    </x-rapidez::button>
                    <x-rapidez::button href="/checkout">
                        @lang('Checkout')
                    </x-rapidez::button>
                </div>
            </div>
        </div>
        <a href="/cart" aria-label="@lang('Cart')" class="my-1" v-else>
            <x-heroicon-o-shopping-cart class="h-6 w-6"/>
        </a>
    </toggler>
</minicart>
