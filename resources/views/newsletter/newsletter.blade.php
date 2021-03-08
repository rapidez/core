<graphql-mutation query="mutation {subscribeEmailToNewsletter(changes){status}}" >
  <div slot-scope="{mutate, changes}">
    <form v-on:submit.prevent="mutate">
      <div>
        <x-rapidez::input name="email" type="text" v-model="changes.email" id="email" />
      </div>
      <button type="submit">@lang('Subscribe')</button>
    </form>
  </div>
</graphql-mutation>


