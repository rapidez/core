<div class="max-w-full mx-auto px-5 pt-12 lg:pt-16">
    <div class="px-6 py-6 bg-gray-200 rounded-lg md:py-12 md:px-12 lg:py-16 lg:px-16 xl:flex xl:items-center">
        <div class="xl:w-0 xl:flex-1">
            <strong class="text-2xl font-extrabold tracking-tight text-gray-600 sm:text-3xl">
              @lang('Want product news and updates?')
            </strong>
            <p class="mt-3 max-w-3xl text-lg leading-6 text-gray-600">
                @lang('Sign up for our newsletter to stay up to date.')
            </p>
        </div>
        <div class="sm:w-full sm:max-w-md xl:mt-0 xl:ml-8" dusk="newsletter">
            <lazy-component>
                <graphql-mutation v-cloak query="mutation visitor ($email: String!) { subscribeEmailToNewsletter(email: $email) { status } }" :alert="false" :clear="true">
                    <div slot-scope="{ mutate, variables, mutated, error }">
                        <p v-if="mutated" class="text-primary text-xl font-bold">
                            @lang('Thank you for subscribing!')
                        </p>
                        <div v-else>
                            <form class="sm:flex mt-4" v-on:submit.prevent="mutate">
                                <x-rapidez::input
                                    label=""
                                    name="email"
                                    type="email"
                                    v-model="variables.email"
                                    class="px-5 py-3 !text-base"
                                    wrapperClass="flex-grow"
                                    dusk="newsletter-email"
                                    autocomplete="email"
                                    placeholder="Enter your email"
                                    required
                                />

                                <x-rapidez::button
                                    type="submit"
                                    dusk="newsletter-submit"
                                    class="w-full mt-3 px-5 py-3 sm:ml-5 sm:w-auto sm:mt-0"
                                >
                                    @lang('Subscribe')
                                </x-rapidez::button>
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
            </lazy-component>
        </div>
    </div>
</div>
