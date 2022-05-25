<div class="flex-grow text-sm flex justify-between" slot="renderResultStats" slot-scope="{ numberOfResults, numberOfPages, currentPage, time }">
    @{{ numberOfResults }} @lang('products')
    <template v-if="numberOfPages > 1">
        (@lang('page'): @{{ currentPage + 1 }}/@{{ numberOfPages }})
    </template>

    <div class="justify-self-end mr-1">
        <select class="css-oswg5g !outline-none !rounded shadow focus:ring focus:ring-green-500" v-model="$root.listingResultsPerPage">
            <option v-for="s in [12, 24, 36, window.config.translations.results.all]" v-bind:value="s"> @{{ s }}</option>
        </select>
    </div>
</div>
