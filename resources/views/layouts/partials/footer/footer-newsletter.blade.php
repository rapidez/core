<div class="text-red-light mb-2">@lang('Sign up for our newsletter to stay up to date.')</div>
<div class="mt-4 flex">
    <graphql-mutation query="mutation { subscribeEmailToNewsletter(changes) { status } }" :alert="false" :clear="false">
        <div class="mt-4" slot-scope="{ mutate, changes, mutated, error }">
            <form class="flex flex-col justify-around" v-on:submit.prevent="mutate">
                <x-rapidez::input
                label=""
                name="email"
                type="email"
                v-model="changes.email"
                class="px-5 py-3 w-full !text-base"
                wrapperClass="flex-grow"
                dusk="newsletter-email"
                autocomplete="email"
                placeholder="Enter your email"
                required
                />
                <x-rapidez::button
                type="submit"
                dusk="newsletter-submit"
                class="w-full text-sm sm:w-auto sm:mt-0 md:mt-4"
                >
                @lang('Subscribe')
                </x-rapidez::button>
            </form>
        </div>
    </graphql-mutation>
</div>
