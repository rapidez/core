<graphql query="{ checkoutAgreements { agreement_id name checkbox_text content is_html mode } }" cache="checkout.agreements" v-cloak>
    <div slot-scope="{ data }" v-if="data">
        <div v-for="agreement in data.checkoutAgreements" :key="agreement.agreement_id">
            <x-rapidez::slideover position="right" v-bind:id="agreement.agreement_id + '_agreement'">
                <x-slot:title>
                    @{{ agreement.name }}
                </x-slot:title>
                <div v-if="agreement.is_html" v-html="agreement.content" class="p-3"></div>
                <div v-else v-text="agreement.content" class="whitespace-pre-line p-3"></div>
            </x-rapidez::slideover>

            <template v-if="agreement.mode == 'AUTO'">
                <label class="text-gray-700 cursor-pointer underline hover:no-underline" v-bind:for="agreement.agreement_id + '_agreement'">
                    @{{ agreement.checkbox_text }}
                </label>
            </template>
            <div v-else>
                <x-rapidez::checkbox
                    name="agreement_ids[]"
                    v-bind:value="agreement.agreement_id"
                    dusk="agreements"
                    required
                >
                    <label class="text-gray-700 cursor-pointer underline hover:no-underline" v-bind:for="agreement.agreement_id + '_agreement'">
                        @{{ agreement.checkbox_text }}
                    </label>
                </x-rapidez::checkbox>
            </div>
        </div>
    </div>
</graphql>
