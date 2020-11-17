<div class="flex-grow text-sm" slot="renderResultStats" slot-scope="{ numberOfResults, numberOfPages, currentPage, time }">
    @{{ numberOfResults }} @lang('products')
    <template v-if="numberOfPages > 1">
        (@lang('page'): @{{ currentPage + 1 }}/@{{ numberOfPages }})
    </template>
</div>
