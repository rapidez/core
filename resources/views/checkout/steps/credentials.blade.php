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

        <div class="mt-2">
            @include('rapidez::checkout.partials.create-account')
        </div>
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

    <x-rapidez::button type="submit" class="mt-3" dusk="continue">
        @lang('Continue')
    </x-rapidez::button>
</form>
