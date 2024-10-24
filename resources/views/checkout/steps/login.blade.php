<login v-slot="{ email, password, go, loginInputChange, emailAvailable }">
    <div class="flex justify-center">
        <form class="p-8 w-[400px]" v-on:submit.prevent="go()">
            <h1 class="font-bold text-4xl text-center mb-5">@lang('Checkout')</h1>

            <x-rapidez::input
                :label="false"
                name="email"
                type="email"
                placeholder="Email"
                v-bind:value="email"
                v-on:input="loginInputChange"
                required
            />
            <div class="relative">
                <x-rapidez::input
                    v-if="!emailAvailable"
                    :label="false"
                    class="mt-3"
                    name="password"
                    type="password"
                    placeholder="Password"
                    ref="password"
                    v-on:input="loginInputChange"
                    required
                />

                <input
                    type="checkbox"
                    v-if="!emailAvailable"
                    oninvalid="this.setCustomValidity('{{ __('Please log in') }}')"
                    class="absolute h-full inset-0 opacity-0 pointer-events-none"
                    required
                />
            </div>

            <x-rapidez::button type="submit" class="w-full mt-5" dusk="continue">
                @lang('Continue')
            </x-rapidez::button>

            @if (App::providerIsLoaded('Rapidez\Account\AccountServiceProvider'))
                <a href="{{ route('account.forgotpassword') }}" class="inline-block text-sm hover:underline mt-5" v-if="!emailAvailable">
                    @lang('Forgot your password?')
                </a>
            @endif
        </form>
    </div>
</login>
