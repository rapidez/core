<ais-stats>
    <template v-slot="{ nbPages, nbHits, page, processingTimeMS }">
        @{{ nbHits }} @lang('products')
        <template v-if="window.debug">
            - @{{ processingTimeMS }}ms
        </template>
    </template>
</ais-stats>
