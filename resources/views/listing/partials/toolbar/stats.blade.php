<ais-stats>
    <template v-slot="{ nbPages, nbHits, page, processingTimeMS }">
        @{{ nbHits }} @lang('products')
        <template v-if="nbPages > 1">
            (@lang('page'): @{{ page + 1 }} / @{{ nbPages }})
        </template>
        <template v-if="window.debug">
            - @{{ processingTimeMS }}ms
        </template>
    </template>
</ais-stats>
