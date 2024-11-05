<h1 class="mb-5 text-4xl font-bold">@lang('Credentials')</h1>

<form v-on:submit.prevent="save(['credentials'], 3)" class="flex flex-col gap-5 rounded bg-highlight p-4 md:p-8">
    <div class="flex flex-col gap-2">
        <div class="grid grid-cols-12 gap-4">
            <p class="col-span-12 text-2xl font-bold">
                @lang('Shipping address')
            </p>
            @include('rapidez::checkout.partials.address', ['type' => 'shipping'])
        </div>
        <div class="col-span-12 my-5">
            <x-rapidez::checkbox v-model="checkout.hide_billing">
                @lang('My billing and shipping address are the same')
            </x-rapidez::checkbox>
        </div>
        <div v-if="!checkout.hide_billing" class="grid grid-cols-12 gap-4">
            <p class="col-span-12 text-2xl font-bold">
                @lang('Billing address')
            </p>
            @include('rapidez::checkout.partials.address', ['type' => 'billing'])
        </div>
    </div>

    <div class="flex flex-col gap-4">
        <p class="text-2xl font-bold">
            @lang('Shipping method')
        </p>
        <template v-for="(method, index) in checkout.shipping_methods">
            <x-rapidez::radio v-model="checkout.shipping_method" v-bind:value="method.carrier_code+'_'+method.method_code" v-bind:dusk="'shipping-method-'+index">
                @{{ method.method_title }}
            </x-rapidez::radio>
        </template>
    </div>
    <x-rapidez::button.enhanced type="submit" dusk="continue" class="self-start">
        @lang('Continue')
    </x-rapidez::button.enhanced>
</form>
