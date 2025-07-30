<checkout-login v-slot="checkoutLogin">
    <fieldset partial-submit v-on:partial-submit="async () => await go()" class="flex flex-col gap-3" v-cloak>
        <label>
            <x-rapidez::label>@lang('Email')</x-rapidez::label>
            <x-rapidez::input
                name="email"
                type="email"
                v-model="checkoutLogin.email"
                v-bind:disabled="window.app.config.globalProperties.loggedIn.value"
                required
            />
        </label>
        <template v-if="!window.app.config.globalProperties.loggedIn.value && (!checkoutLogin.isEmailAvailable || checkoutLogin.createAccount)">
            <label>
                <x-rapidez::label>@lang('Password')</x-rapidez::label>
                <x-rapidez::input.password
                    name="password"
                    v-model="checkoutLogin.password"
                    required
                />
            </label>
        </template>
        @if (App::providerIsLoaded('Rapidez\Account\AccountServiceProvider'))
            <a href="{{ route('account.forgotpassword') }}" class="inline-block text-sm hover:underline mt-5" v-if="!checkoutLogin.isEmailAvailable">
                @lang('Forgot your password?')
            </a>
        @endif
        <template v-if="!window.app.config.globalProperties.loggedIn.value && checkoutLogin.createAccount">
            <label>
                <x-rapidez::label>@lang('Repeat password')</x-rapidez::label>
                <x-rapidez::input.password
                    name="password_repeat"
                    v-model="checkoutLogin.password_repeat"
                    required
            />
            </label>
            <label>
                <x-rapidez::label>@lang('Firstname')</x-rapidez::label>
                <x-rapidez::input
                    name="firstname"
                    type="text"
                    v-model="checkoutLogin.firstname"
                    required
                />
            </label>
            <label>
                <x-rapidez::label>@lang('Lastname')</x-rapidez::label>
                <x-rapidez::input
                    name="lastname"
                    type="text"
                    v-model="checkoutLogin.lastname"
                    required
                />
            </label>
        </template>
        <template v-if="!window.app.config.globalProperties.loggedIn.value && checkoutLogin.isEmailAvailable">
            <x-rapidez::input.checkbox v-model="checkoutLogin.createAccount" dusk="create_account">
                @lang('Create an account')
            </x-rapidez::input.checkbox>
        </template>
    </fieldset>
</checkout-login>
