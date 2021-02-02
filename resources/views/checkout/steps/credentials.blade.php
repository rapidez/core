<h1 class="font-bold text-4xl mb-5">@lang('Credentials')</h1>

<form class="lg:w-2/3" v-on:submit.prevent="save(['credentials'], 3)">
    <div class="grid grid-cols-12 gap-4 mb-3">
        <p class="col-span-12 font-bold text-2xl">
            @lang('Shipping address')
        </p>
        <div class="col-span-12 sm:col-span-6">
            <x-rapidez::input name="firstname" v-model="checkout.shipping_address.firstname" required/>
        </div>
        <div class="col-span-12 sm:col-span-6">
            <x-rapidez::input name="lastname" v-model="checkout.shipping_address.lastname" required/>
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-rapidez::input name="zipcode" v-model="checkout.shipping_address.zipcode" required/>
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-rapidez::input name="housenumber" v-model="checkout.shipping_address.housenumber" :placeholder="__('Nr.')" required/>
        </div>
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <x-rapidez::input name="street" v-model="checkout.shipping_address.street" required/>
        </div>
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <x-rapidez::input name="city" v-model="checkout.shipping_address.city" required/>
        </div>
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <x-rapidez::input name="telephone" v-model="checkout.shipping_address.telephone" required/>
        </div>
    </div>

    <div class="col-span-12">
        <input type="checkbox" v-model="checkout.hide_billing" id="hide_billing">
        <label for="hide_billing">@lang('My billing and shipping address are the same')</label>
    </div>
    <div v-if="!checkout.hide_billing" class="grid grid-cols-12 gap-4 mb-3">
        <p class="col-span-12 font-bold text-2xl">
            @lang('Billing address')
        </p>
        <div class="col-span-12 sm:col-span-6">
            <x-rapidez::input name="firstname" v-bind:required="!checkout.hide_billing" v-model="checkout.billing_address.firstname"/>
        </div>
        <div class="col-span-12 sm:col-span-6">
            <x-rapidez::input name="lastname" v-bind:required="!checkout.hide_billing" v-model="checkout.billing_address.lastname"/>
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-rapidez::input name="zipcode" v-bind:required="!checkout.hide_billing" v-model="checkout.billing_address.zipcode"/>
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-rapidez::input name="housenumber" v-bind:required="!checkout.hide_billing" v-model="checkout.billing_address.housenumber" :placeholder="__('Nr.')"/>
        </div>
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <x-rapidez::input name="street" v-bind:required="!checkout.hide_billing" v-model="checkout.billing_address.street"/>
        </div>
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <x-rapidez::input name="city" v-bind:required="!checkout.hide_billing" v-model="checkout.billing_address.city"/>
        </div>
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <x-rapidez::input name="telephone" v-bind:required="!checkout.hide_billing" v-model="checkout.billing_address.telephone"/>
        </div>
    </div>

    <h1 v-if="checkout.shipping_methods.length" class="font-bold text-4xl mt-5 mb-3">@lang('Shipping method')</h1>

    <div class="my-2" v-for="method in checkout.shipping_methods">
        <input
            type="radio"
            name="shipping_method"
            :value="method.carrier_code+'_'+method.method_code"
            :id="method.carrier_code+'_'+method.method_code"
            v-model="checkout.shipping_method"
        >
        <label :for="method.carrier_code+'_'+method.method_code">@{{ method.method_title }}</label>
    </div>

    <p v-if="!user" class="col-span-12 font-bold text-2xl">
        @lang('Create account')
    </p>
    <div class="grid grid-cols-12 gap-4 mb-3" v-if="!user">
        <div class="col-span-12" v-if="!checkout.hasVirtualItems">
            <input type="checkbox" v-model="checkout.create_account" id="create_account">
            <label for="create_account">@lang('Create an account')</label>
        </div>
        <div class="col-span-12 sm:col-span-6" v-if="checkout.create_account || checkout.hasVirtualItems">
            <x-rapidez::input name="password" type="password" v-model="checkout.password" required />
        </div>
        <div class="col-span-12 sm:col-span-6" v-if="checkout.create_account || checkout.hasVirtualItems">
            <x-rapidez::input name="password_repeat" type="password" v-model="checkout.password_repeat" required />
        </div>
    </div>

    <button
        type="submit"
        class="btn btn-primary mt-3"
        :disabled="$root.loading"
        dusk="continue"
    >
        @lang('Continue')
    </button>
</form>
