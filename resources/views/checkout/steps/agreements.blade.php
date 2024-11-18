<graphql query="{ checkoutAgreements { agreement_id name checkbox_text content is_html mode } }" cache="checkout.agreements" v-cloak>
    <div slot-scope="{ data }" v-if="data">
        <div v-for="agreement in data.checkoutAgreements" :key="agreement.agreement_id">
            <global-slideover v-bind:title="agreement.name" position="right" v-bind:contents="'<div class=&quot;' + (agreement.is_html ? '' : 'whitespace-pre-line ') + 'p-3&quot;>' + agreement.content + '</div>'">
                <template v-slot="slideover">
                    <template v-if="agreement.mode == 'AUTO'">
                        <label class="text-gray-700 cursor-pointer underline hover:no-underline" v-on:click="slideover.open" for="slideover-global">
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
                            <label class="text-gray-700 cursor-pointer underline hover:no-underline" v-on:click="slideover.open" for="slideover-global">
                                @{{ agreement.checkbox_text }}
                            </label>
                        </x-rapidez::checkbox>
                    </div>
                </template>
            </global-slideover>
        </div>
    </div>
</graphql>
