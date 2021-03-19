<user class="mr-3" v-cloak>
    <div v-if="$root.user" class="group" dusk="account_menu" slot-scope="{ logout }">
        <div class="flex my-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            @{{ $root.user.firstname }}
        </div>
        <div class="hidden absolute bg-white border shadow rounded mr-1 z-10 group-hover:block {{ Route::currentRouteName() == 'checkout' ? 'right-0' : '' }}">
            @if(App::providerIsLoaded('Rapidez\Account\AccountServiceProvider'))
                <a class="block hover:bg-secondary px-3 py-2" href="/account">@lang('Account')</a>
                <a class="block hover:bg-secondary px-3 py-2" href="/account/orders">@lang('Orders')</a>
            @endif
            <a
                href="#"
                class="block hover:bg-secondary px-3 py-2"
                dusk="logout"
                @click.prevent="logout()"
            >
                @lang('Logout')
            </a>
        </div>
    </div>
    @if(App::providerIsLoaded('Rapidez\Account\AccountServiceProvider'))
        <div class="my-1" v-else>
            <a href="/login" aria-label="@lang('Login')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </a>
        </div>
    @endif
</user>
