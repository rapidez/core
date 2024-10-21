<div class="mr-3">
    <toggler v-if="$root.loggedIn" v-cloak>
        <div slot-scope="accountTogglerScope" v-on-click-away="accountTogglerScope.close">
            <button dusk="account_menu" class="flex my-1" v-on:click="accountTogglerScope.toggle">
                <x-heroicon-o-user class="h-6 w-6"/>
                @{{ $root.user.firstname }}
            </button>
            <div v-if="accountTogglerScope.isOpen" class="absolute bg-white border shadow rounded mr-1 {{ config('rapidez.frontend.z-indexes.header-dropdowns') }} {{ Route::currentRouteName() == 'checkout' ? 'right-0' : '' }}">
                @if (App::providerIsLoaded('Rapidez\Account\AccountServiceProvider'))
                    <a class="block hover:bg-inactive px-3 py-2" href="{{ route('account.overview') }}">@lang('Account')</a>
                    <a class="block hover:bg-inactive px-3 py-2" href="{{ route('account.orders') }}">@lang('Orders')</a>
                    @if (App::providerIsLoaded('Rapidez\Wishlist\WishlistServiceProvider'))
                        <a class="block hover:bg-inactive px-3 py-2" href="{{ route('account.wishlist') }}">@lang('Wishlist')</a>
                    @endif
                @endif
                <user>
                    <button
                        class="block hover:bg-inactive px-3 py-2 cursor-pointer"
                        dusk="logout"
                        slot-scope="userLogoutScope"
                        @click="userLogoutScope.logout('/')"
                    >
                        @lang('Logout')
                    </button>
                </user>
            </div>
        </div>
    </toggler>
    @if (App::providerIsLoaded('Rapidez\Account\AccountServiceProvider'))
        <div class="my-1" v-else>
            <a href="{{ route('account.login') }}" aria-label="@lang('Login')">
                <x-heroicon-o-user class="h-6 w-6"/>
            </a>
        </div>
    @endif
</div>
