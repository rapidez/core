<toggler>
    <div class="relative" v-if="hasCart" v-on-click-away="close" slot-scope="{ toggle, close, isOpen }">
        <button class="flex my-1 focus:outline-none" v-on:click="toggle">
            <x-heroicon-o-shopping-cart class="size-6"/>
            <span class="bg-secondary text-secondary-text rounded-full size-6 text-center" dusk="minicart-count">
                <span v-cloak>@{{ Math.round(cart.total_quantity) }}</span>
            </span>
        </button>
        <div v-if="isOpen" class="absolute right-0 bg-white border shadow rounded-xl p-5 z-header-minicart" v-cloak>
            <table class="w-full mb-3">
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
                <tr class="font-bold *:pt-3 border-t">
                    <td colspan="2">@lang('Total')</td>
                    <td class="text-right">@{{ cart.prices.grand_total.value | price }}</td>
                </tr>
            </table>
            <div class="flex justify-between items-center">
                <x-rapidez::button.outline href="{{ route('cart') }}" class="mr-5 whitespace-nowrap">
                    @lang('Show cart')
                </x-rapidez::button.outline>

                <div class="w-full" :class="{ 'cursor-not-allowed': !canOrder }">
                    <x-rapidez::button.conversion href="{{ route('checkout') }}" v-bind:class="{ 'pointer-events-none': !canOrder }">
                        @lang('Checkout')
                    </x-rapidez::button.conversion>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('cart') }}" aria-label="@lang('Cart')" class="my-1" v-else v-cloak>
        <x-heroicon-o-shopping-cart class="size-6"/>
    </a>
</toggler>
