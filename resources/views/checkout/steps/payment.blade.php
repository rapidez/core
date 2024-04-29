<h1 class="font-bold text-4xl mb-5">@lang('Payment method')</h1>
<form class="bg-highlight p-8 rounded mt-6" v-on:submit.prevent="save(['payment_method'], 4)">
    <div class="my-2 border bg-white p-4 rounded" v-for="(method, index) in checkout.payment_methods">
        <x-rapidez::radio
            v-bind:value="method.code"
            v-bind:dusk="'method-'+index"
            v-model="checkout.payment_method"
            class="[&+div]:flex-1"
        >
            <div class="flex items-center">
                <span>@{{ method.title }}</span>
                <img
                    class="ml-auto w-8 h-8"
                    v-bind:src="`/payment-icons/${method.code}.svg`"
                    onerror="this.onerror=null; this.src=`/payment-icons/default.svg`"
                >
            </div>
        </x-rapidez::radio>
    </div>

    <graphql query="{ checkoutAgreements { agreement_id name checkbox_text content is_html mode } }">
        <div slot-scope="{ data }" v-if="data" class="mt-5">
            <template v-if="$root?.openAgreement">
                <x-rapidez::slideover position="right" id="checkoutAgreement">
                    <x-slot:title>
                        @{{ $root.openAgreement.name }}
                    </x-slot:title>
                    <div v-if="$root.openAgreement.is_html" v-html="$root.openAgreement.content"></div>
                    <div v-else v-text="$root.openAgreement.content" class="whitespace-pre-line"></div>
                </x-rapidez::slideover>
            </template>
            <div v-for="agreement in data.checkoutAgreements" :key="agreement.agreement_id">
                <label class="text-gray-700 cursor-pointer underline hover:no-underline" for="checkoutAgreement" v-if="agreement.mode == 'AUTO'" v-on:click="$root.openAgreement = agreement">
                    @{{ agreement.checkbox_text }}
                </label>
                <div v-else>
                    <x-rapidez::checkbox
                        name="agreement_ids[]"
                        v-bind:value="agreement.agreement_id"
                        v-model="checkout.agreement_ids"
                        dusk="agreements"
                        required
                    >
                        <label class="text-gray-700 cursor-pointer underline hover:no-underline" for="checkoutAgreement" v-on:click="$root.openAgreement = agreement">
                            @{{ agreement.checkbox_text }}
                        </label>
                    </x-rapidez::checkbox>
                </div>
            </div>
        </div>
    </graphql>

    <x-rapidez::button type="submit" class="mt-5" dusk="continue">
        @lang('Place order')
    </x-rapidez::button>
</form>
