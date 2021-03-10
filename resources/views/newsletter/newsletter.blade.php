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
        <div class="mt-8 sm:w-full sm:max-w-md xl:mt-0 xl:ml-8">
          <form class="sm:flex" v-on:submit.prevent="mutate">
            <input id="email" name="emailAddress" type="email" v-model="changes.email" autocomplete="email" required class="w-full border-white px-5 py-3 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-indigo-700 focus:ring-white rounded-md" placeholder="@lang('Enter your email')" />
            <button type="submit" class="mt-3 w-full flex items-center justify-center px-5 py-3 border border-transparent shadow text-base font-medium rounded-md text-white btn btn-primary hover:bg-primary-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-primary-700 focus:ring-white sm:mt-0 sm:ml-3 sm:w-auto sm:flex-shrink-0" :disabled="$root.loading">
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

