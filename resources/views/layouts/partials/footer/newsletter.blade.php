@if(Route::currentRouteName() !== 'checkout')
    <graphql-mutation v-cloak query="mutation {subscribeEmailToNewsletter(changes){status}}" >
        <div class="max-w-full mx-auto px-4 pt-12 sm:px-6 lg:pt-16 lg:px-8" slot-scope="{mutate, changes, mutated}">
            <div class="px-6 py-6 bg-gray-200 rounded-lg md:py-12 md:px-12 lg:py-16 lg:px-16 xl:flex xl:items-center">
                <div class="xl:w-0 xl:flex-1">
                    <h2 class="text-2xl font-extrabold tracking-tight text-gray-600 sm:text-3xl">
                      @lang('Want product news and updates?')
                    </h2>
                    <p class="mt-3 max-w-3xl text-lg leading-6 text-gray-600">
                        @lang('Sign up for our newsletter to stay up to date.')
                    </p>
                </div>
                <div class="sm:w-full sm:max-w-md xl:mt-0 xl:ml-8">
                    <form class="sm:flex mt-4" v-on:submit.prevent="mutate">
                        <x-rapidez::input wrapperClass="flex-grow" label="" id="email" name="emailAddress" type="email" v-model="changes.email" autocomplete="email" required class="h-full px-5 py-3" placeholder="Enter your email" />
                        <button type="submit" class="btn btn-primary px-5 py-3 ml-5" :disabled="$root.loading">
                            @lang('Subscribe')
                        </button>
                    </form>
                    <div v-if="mutated" class="ml-3 text-green-500">
                        @lang('Subscribed successfully!')
                    </div>
                    <p class="mt-3 text-sm text-gray-600">
                        @lang('We care about the protection of your data. Read our')
                        <a href="/privacy-policy-cookie-restriction-mode" class="text-gray-400 font-medium underline">
                            @lang('Privacy Policy.')
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </graphql-mutation>
@endif

