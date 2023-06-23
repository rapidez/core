<div class="mt-4 md:mt-0">
    <h3 class="text-base font-bold text-gray-900">@lang('Want product news and updates?')</h3>
    <p class="mt-4 text-base text-gray-500">@lang('Sign up for our newsletter to stay up to date.')</p>
    <div class="sm:w-full sm:max-w-md xl:mt-0" dusk="newsletter">
        <lazy>
            <graphql-mutation v-cloak query="mutation visitor ($email: String!) { subscribeEmailToNewsletter(email: $email) { status } }" :alert="false" :clear="true">
                <div slot-scope="{ mutate, variables, mutated, error }">
                    <p v-if="mutated" class="text-neutral text-xl font-bold">
                        @lang('Thank you for subscribing!')
                    </p>
                    <div v-else>
                        <form class="mt-4 sm:flex sm:max-w-md" v-on:submit.prevent="mutate">
                            <x-rapidez::input
                                :label="false"
                                name="email"
                                type="email"
                                v-model="variables.email"
                                class="w-full min-w-0 appearance-none rounded-md border border-gray-300 bg-white py-2 px-4 text-base text-gray-900 placeholder-gray-500 shadow-sm focus:border-indigo-500 focus:placeholder-gray-400 focus:outline-none focus:ring-indigo-500"
                                wrapperClass="flex-grow"
                                dusk="newsletter-email"
                                autocomplete="email"
                                placeholder="Enter your email"
                                required
                            />
                            <div class="mt-3 rounded-md sm:mt-0 sm:ml-3 sm:flex-shrink-0">
                                <x-rapidez::button
                                    type="submit"
                                    dusk="newsletter-submit"
                                    class="w-full"
                                >
                                    @lang('Subscribe')
                                </x-rapidez::button>
                            </div>
                        </form>
                        <p v-if="error" class="mt-3 text-sm text-red-700">
                            @{{ error }}
                        </p>
                        <p class="mt-3 text-sm text-gray-600">
                            @lang('We care about the protection of your data. Read our')
                            <a href="/privacy-policy-cookie-restriction-mode" class="underline">
                                @lang('Privacy Policy')
                            </a>
                        </p>
                    </div>
                </div>
            </graphql-mutation>
        </lazy>
    </div>
</div>
