<h1 class="font-bold text-4xl mb-5">@lang('Credentials')</h1>

<form class="lg:w-2/3" v-on:submit.prevent="save(['credentials'], 3)">
    <div class="grid grid-cols-12 gap-4 mb-3">
        <p class="col-span-12 font-bold text-2xl">
            @lang('Shipping address')
        </p>
        @include('rapidez::checkout.partials.form', ['type' => 'shipping'])
    </div>

    <div class="col-span-12 my-5">
        <x-rapidez::checkbox v-model="checkout.hide_billing">
            @lang('My billing and shipping address is the same')
        </x-rapidez::checkbox>
    </div>
    <div v-if="!checkout.hide_billing" class="grid grid-cols-12 gap-4 mb-3">
        <p class="col-span-12 font-bold text-2xl">
            @lang('Billing address')
        </p>
        @include('rapidez::checkout.partials.form', ['type' => 'billing'])
    </div>

    <h1 v-if="checkout.shipping_methods.length" class="font-bold text-4xl mt-5 mb-3">@lang('Shipping method')</h1>

    <div class="my-2" v-for="method in checkout.shipping_methods">
        <x-rapidez::radio
            v-bind:value="method.carrier_code+'_'+method.method_code"
            v-model="checkout.shipping_method"
        >
            @{{ method.method_title }}
        </x-rapidez::radio>
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
