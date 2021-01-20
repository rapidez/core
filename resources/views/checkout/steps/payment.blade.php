<h1 class="font-bold text-4xl mb-5">@lang('Payment method')</h1>
<form v-on:submit.prevent="save(['payment_method'], 4)">
    <div class="my-2" v-for="(method, index) in checkout.payment_methods">
        <input
            type="radio"
            name="payment_method"
            :value="method.code"
            :id="method.code"
            :dusk="'method-'+index"
            v-model="checkout.payment_method"
        >
        <label :for="method.code">@{{ method.title }}</label>
    </div>

    <graphql query="{ checkoutAgreements { agreement_id name checkbox_text content is_html mode } }">
        <div v-if="data" slot-scope="{ data }">
            <div v-for="agreement in data.checkoutAgreements">
                <x-rapidez::slideover>
                    <x-slot name="button">
                        <input type="checkbox" v-if="agreement.mode == 'AUTO'" checked disabled>
                        <input
                            v-else
                            type="checkbox"
                            name="agreement_ids[]"
                            :value="agreement.agreement_id"
                            :id="'agreement_'+agreement.agreement_id"
                            v-model="checkout.agreement_ids"
                            required
                        >
                        <label :for="'agreement_'+agreement.agreement_id">
                            <a href="#" v-on:click.prevent="toggle">@{{ agreement.checkbox_text }}</a>
                        </label>
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

    <button
        type="submit"
        class="btn btn-primary mt-5"
        :disabled="$root.loading"
        dusk="continue"
    >
        @lang('Continue')
    </button>
</form>
