<checkout-login v-slot="checkoutLogin">
    <fieldset data-function="go" v-cloak>
        <x-rapidez::input
            label="Email"
            name="email"
            type="email"
            v-model="checkoutLogin.email"
            v-bind:disabled="loggedIn"
            required
        />
        <template v-if="!loggedIn && (!checkoutLogin.isEmailAvailable || checkoutLogin.createAccount)">
            <x-rapidez::input
                label="Password"
                name="password"
                type="password"
                v-model="checkoutLogin.password"
                required
            />
        </template>
        @if (App::providerIsLoaded('Rapidez\Account\AccountServiceProvider'))
            <a href="{{ route('account.forgotpassword') }}" class="inline-block text-sm hover:underline mt-5" v-if="!checkoutLogin.isEmailAvailable">
                @lang('Forgot your password?')
            </a>
        @endif
        <template v-if="!loggedIn && checkoutLogin.createAccount">
            <x-rapidez::input
                label="Repeat password"
                name="password_repeat"
                type="password"
                v-model="checkoutLogin.password_repeat"
                required
            />
            <x-rapidez::input
                label="Firstname"
                name="firstname"
                type="text"
                v-model="checkoutLogin.firstname"
                required
            />
            <x-rapidez::input
                label="Lastname"
                name="lastname"
                type="text"
                v-model="checkoutLogin.lastname"
                required
            />
        </template>
        <template v-if="!loggedIn && checkoutLogin.isEmailAvailable">
            <x-rapidez::checkbox v-model="checkoutLogin.createAccount" dusk="create_account">@lang('Create an account')</x-rapidez::checkbox>
        </template>
    </fieldset>
</checkout-login>
