<graphql query="{ checkoutAgreements { agreement_id name checkbox_text content is_html mode } }" cache="checkout.agreements" v-cloak>
    <div slot-scope="{ data }" v-if="data">
        <div v-for="agreement in data.checkoutAgreements" :key="agreement.agreement_id">
            <global-slideover :title="agreement.name"
                v-slot="slideover">
                <div>
                    <div class="hidden">
                        <teleport to="#global-slideover-content" :disabled="!slideover.isCurrentSlideover">
                            <div v-bind:class="{'p-3': true, 'whitespace-pre-line': !agreement.is_html}" v-html="agreement.content"></div>
                        </teleport>
                    </div>
                    <template v-if="agreement.mode == 'AUTO'">
                        <label class="text-gray-700 cursor-pointer underline hover:no-underline" v-on:click="slideover.open">
                            @{{ agreement.checkbox_text }}
                        </label>
                    </template>
                    <template v-else>
                        <x-rapidez::input.checkbox
                            name="agreement_ids[]"
                            v-bind:value="agreement.agreement_id"
                            dusk="agreements"
                            required
                        >
                            <label class="text-gray-700 cursor-pointer underline hover:no-underline" v-on:click="slideover.open">
                                @{{ agreement.checkbox_text }}
                            </label>
                        </x-rapidez::input.checkbox>
                    </template>
                </div>
            </global-slideover>
        </div>
    </div>
</graphql>
