<h1 class="font-bold text-4xl mb-5">@lang('Payment method')</h1>
<form class="bg-highlight p-8 rounded mt-6" v-on:submit.prevent="save(['payment_method'], 4)">
    <label class="flex items-center gap-2 my-2 border bg-white p-4 rounded" v-for="(method, index) in checkout.payment_methods">
        <x-rapidez::input.radio
            v-bind:value="method.code"
            v-bind:dusk="'method-'+index"
            v-model="checkout.payment_method"
        />
        <span>@{{ method.title }}</span>
        <img
            class="ml-auto w-8 h-8"
            v-bind:src="`/payment-icons/${method.code}.svg`"
            onerror="this.onerror=null; this.src=`/payment-icons/default.svg`"
        >
    </label>

    <graphql query="{ checkoutAgreements { agreement_id name checkbox_text content is_html mode } }">
        <div v-if="data" slot-scope="{ data }" class="mt-5">
            <div v-for="agreement in data.checkoutAgreements">
                <x-rapidez::slideover>
                    <x-slot name="button">
                        <a class="text-gray-700" href="#" v-on:click.prevent="toggle" v-if="agreement.mode == 'AUTO'">
                            @{{ agreement.checkbox_text }}
                        </a>
                        <div v-else>
                            <x-rapidez::input.checkbox
                                name="agreement_ids[]"
                                v-bind:value="agreement.agreement_id"
                                v-model="checkout.agreement_ids"
                                dusk="agreements"
                                required
                            >
                                <a href="#" v-on:click.prevent="toggle">@{{ agreement.checkbox_text }}</a>
                            </x-rapidez::input.checkbox>
                        </div>
                    </x-slot>

                    <x-slot name="title">
                        @{{ agreement.name }}
                    </x-slot>

                    <div v-if="agreement.is_html" v-html="agreement.content"></div>
                    <div v-else v-text="agreement.content" class="whitespace-pre-line"></div>
                </x-rapidez::slideover>
            </div>
        </div>
    </graphql>

    <x-rapidez::button type="submit" class="mt-5" dusk="continue">
        @lang('Place order')
    </x-rapidez::button>
</form>
