<div class="mt-4 lg:mt-0 text-muted lg:w-1/3">
    <h3 class="text-base font-bold">@lang('Want product news and updates?')</h3>
    <p class="mt-4 text-base">@lang('Sign up for our newsletter to stay up to date.')</p>
    <div class="sm:w-full sm:max-w-md xl:mt-0" dusk="newsletter">
        <lazy>
            <graphql-mutation query="mutation visitor ($email: String!) { subscribeEmailToNewsletter(email: $email) { status } }" :alert="false" :clear="true">
                <div slot-scope="{ mutate, variables, mutated, error }">
                    <p v-if="mutated" class="text text-xl font-bold" v-cloak>
                        @lang('Thank you for subscribing!')
                    </p>
                    <div v-else>
                        <form class="mt-4 sm:flex sm:max-w-md" v-on:submit.prevent="mutate">
                            <x-rapidez::input
                                name="email"
                                type="email"
                                v-model="variables.email"
                                dusk="newsletter-email"
                                autocomplete="email"
                                placeholder="Enter your email"
                                required
                            />
                            <div class="mt-3 rounded-md sm:mt-0 sm:ml-3 sm:flex-shrink-0">
                                <x-rapidez::button.secondary
                                    type="submit"
                                    dusk="newsletter-submit"
                                    class="w-full static"
                                >
                                    @lang('Subscribe')
                                </x-rapidez::button.secondary>
                            </div>
                        </form>
                        <p v-if="error" class="mt-3 text-sm text-red-700" v-cloak>
                            @{{ error }}
                        </p>
                        <p class="mt-3 text-sm">
                            @lang('We care about the protection of your data. Read our')
                            <a href="{{ url('/privacy-policy-cookie-restriction-mode') }}" class="underline">
                                @lang('Privacy Policy')
                            </a>
                        </p>
                    </div>
                </div>
            </graphql-mutation>
        </lazy>
    </div>
</div>
