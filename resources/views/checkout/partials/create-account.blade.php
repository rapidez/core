<div v-if="!loggedIn" id="create-account-wrapper">
    <div class="grid grid-cols-12 gap-4 mb-3">
        <div class="col-span-12" v-if="!forceAccount">
            <x-rapidez::input.checkbox v-model="checkout.create_account" dusk="create_account">
                @lang('Create an account')
            </x-rapidez::input.checkbox>
        </div>
        <div class="col-span-12 sm:col-span-6" v-if="checkout.create_account || forceAccount">
            <label>
                <x-rapidez::input.label>@lang('Password')</x-rapidez::input.label>
                <x-rapidez::input.password v-model="checkout.password" required/>
            </label>
        </div>
        <div class="col-span-12 sm:col-span-6" v-if="checkout.create_account || forceAccount">
            <label>
                <x-rapidez::input.label>@lang('Repeat password')</x-rapidez::input.label>
                <x-rapidez::input.password v-model="checkout.password_repeat" required/>
            </label>
        </div>
    </div>
</div>
