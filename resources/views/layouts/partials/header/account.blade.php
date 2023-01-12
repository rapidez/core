<div class="mr-3">
    <toggler v-if="$root.user?.id" v-cloak>
        <div slot-scope="{ toggle, close, isOpen }" v-on-click-away="close">
            <button dusk="account_menu" class="flex my-1" v-on:click="toggle">
                <x-heroicon-o-user class="h-6 w-6"/>
                @{{ $root.user.firstname }}
            </button>
            <div v-if="isOpen" class="absolute bg-white border shadow rounded mr-1 {{ config('rapidez.z-indexes.header-dropdowns')}} {{ Route::currentRouteName() == 'checkout' ? 'right-0' : '' }}">
                @if(App::providerIsLoaded('Rapidez\Account\AccountServiceProvider'))
                    <a class="block hover:bg-secondary px-3 py-2" href="/account">@lang('Account')</a>
                    <a class="block hover:bg-secondary px-3 py-2" href="/account/orders">@lang('Orders')</a>
                @endif
                <user>
                    <a
                        href="#"
                        class="block hover:bg-secondary px-3 py-2"
                        dusk="logout"
                        slot-scope="{ logout }"
                        @click.prevent="logout()"
                    >
                        @lang('Logout')
                    </a>
                </user>
            </div>
        </div>
    </toggler>
    @if(App::providerIsLoaded('Rapidez\Account\AccountServiceProvider'))
        <div class="my-1" v-else>
            <a href="/login" aria-label="@lang('Login')">
                <x-heroicon-o-user class="h-6 w-6"/>
            </a>
        </div>
    @endif
</div>
