<login v-slot="{ email, password, go, loginInputChange, emailAvailable }">
    <div class="flex justify-center">
        <form class="p-8 w-[400px]" v-on:submit.prevent="go()">
            <h1 class="font-bold text-4xl text-center mb-5">@lang('Checkout')</h1>

            <x-rapidez::input-field
                name="email"
                type="email"
                placeholder="Email"
                required
            >
                <x-slot:input v-bind:value="email" v-on:input="loginInputChange"></x-slot:input>
            </x-rapidez::input-field>
            <x-rapidez::input-field
                v-if="!emailAvailable"
                class="mt-3"
                name="password"
                type="password"
                placeholder="Password"
                ref="password"
                required
            >
                <x-slot:input v-on:input="loginInputChange"></x-slot:input>
            </x-rapidez::input-field>

            <x-rapidez::button type="submit" class="w-full mt-5" dusk="continue">
                @lang('Continue')
            </x-rapidez::button>

            @if(App::providerIsLoaded('Rapidez\Account\AccountServiceProvider'))
                <a href="{{ route('account.forgotpassword') }}" class="inline-block text-sm hover:underline mt-5" v-if="!emailAvailable">
                    @lang('Forgot your password?')
                </a>
            @endif
        </form>
    </div>
</login>
