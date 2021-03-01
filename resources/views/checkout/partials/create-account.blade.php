<div v-if="!user" id="create-account-wrapper">
    <div class="grid grid-cols-12 gap-4 mb-3">
        <div class="col-span-12" v-if="!checkout.hasVirtualItems">
            <x-rapidez::checkbox v-model="checkout.create_account">
                @lang('Create an account')
            </x-rapidez::checkbox>
        </div>
        <div class="col-span-12 sm:col-span-6" v-if="checkout.create_account || checkout.hasVirtualItems">
            <x-rapidez::input name="password" type="password" v-model="checkout.password" required />
        </div>
        <div class="col-span-12 sm:col-span-6" v-if="checkout.create_account || checkout.hasVirtualItems">
            <x-rapidez::input name="password_repeat" type="password" v-model="checkout.password_repeat" label="Repeat password" placeholder="Password" required />
        </div>
    </div>
</div>
