<div class="mr-3">
    <toggler v-if="$root.loggedIn" v-cloak>
        <div slot-scope="{ toggle, close, isOpen }" v-on-click-away="close">
            <button dusk="account_menu" class="flex my-1" v-on:click="toggle">
                <x-heroicon-o-user class="size-6"/>
                @{{ $root.user.firstname }}
            </button>
            <div v-if="isOpen" class="absolute bg-white border shadow rounded mr-1 z-header-dropdown">
                @if (App::providerIsLoaded('Rapidez\Account\AccountServiceProvider'))
                    <a class="block hover:bg px-3 py-2" href="{{ route('account.overview') }}">@lang('Account')</a>
                    <a class="block hover:bg px-3 py-2" href="{{ route('account.orders') }}">@lang('Orders')</a>
                    @if (App::providerIsLoaded('Rapidez\Wishlist\WishlistServiceProvider'))
                        <a class="block hover:bg px-3 py-2" href="{{ route('account.wishlist') }}">@lang('Wishlist')</a>
                    @endif
                @endif
                <user>
                    <button
                        class="block hover:bg px-3 py-2 cursor-pointer w-full text-left"
                        dusk="logout"
                        slot-scope="{ logout }"
                        @click="logout('/')"
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
                <x-heroicon-o-user class="size-6"/>
            </a>
        </div>
    @endif
</div>
