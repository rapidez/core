<cart v-cloak >
    <toggler slot-scope="{ cart, hasItems }">
        <div v-if="hasItems" v-on-click-away="close" slot-scope="{toggle, close, isOpen}">
            <button class="flex my-1 focus:outline-none" v-on:click="toggle">
                <x-heroicon-o-shopping-cart class="h-6 w-6"/>
                <span class="bg-primary rounded-full w-6 h-6 text-white text-center" dusk="minicart-count">@{{ Math.round(cart.items_qty) }}</span>
            </button>
            <div v-if="isOpen" class="absolute right-0 bg-white border shadow rounded p-3 mr-1 {{ config('rapidez.z-indexes.header-dropdowns') }}">
                <table class="mb-3">
                    <tr class="py-3" v-for="item in cart.items">
                        <td>@{{ item.name }}</td>
                        <td class="text-right px-4">@{{ item.qty }}</td>
                        <td class="text-right">@{{ item.price | price }}</td>
                    </tr>
                    <tr class="py-3">
                        <td colspan="2">@lang('Total')</td>
                        <td class="text-right">@{{ cart.total | price }}</td>
                    </tr>
                </table>
                <div class="flex justify-between items-center">
                    <x-rapidez::button href="{{ route('cart') }}" variant="outline" class="mr-5">
                        @lang('Show cart')
                    </x-rapidez::button>
                    <x-rapidez::button href="{{ route('checkout') }}">
                        @lang('Checkout')
                    </x-rapidez::button>
                </div>
            </div>
        </div>
        <a href="{{ route('cart') }}" aria-label="@lang('Cart')" class="my-1" v-else>
            <x-heroicon-o-shopping-cart class="h-6 w-6"/>
        </a>
    </toggler>
</cart>
