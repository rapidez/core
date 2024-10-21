<login v-slot="checkoutLoginScope">
    <div class="flex justify-center">
        <form class="p-8 w-[400px]" v-on:submit.prevent="checkoutLoginScope.go">
            <h1 class="font-bold text-4xl text-center mb-5">@lang('Checkout')</h1>

            <x-rapidez::input
                :label="false"
                name="email"
                type="email"
                placeholder="Email"
                v-bind:value="checkoutLoginScope.email"
                v-on:input="checkoutLoginScope.loginInputChange"
                required
            />
            <x-rapidez::input
                v-if="!checkoutLoginScope.emailAvailable"
                :label="false"
                class="mt-3"
                name="password"
                type="password"
                placeholder="Password"
                ref="password"
                v-on:input="checkoutLoginScope.loginInputChange"
                required
            />

            <x-rapidez::button type="submit" class="w-full mt-5" dusk="continue">
                @lang('Continue')
            </x-rapidez::button>

            @if (App::providerIsLoaded('Rapidez\Account\AccountServiceProvider'))
                <a href="{{ route('account.forgotpassword') }}" class="inline-block text-sm hover:underline mt-5" v-if="!checkoutLoginScope.emailAvailable">
                    @lang('Forgot your password?')
                </a>
            @endif
        </form>
    </div>
</login>
