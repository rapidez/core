<div class="flex-wrap flex-1 gap-1 text-base flex justify-between" slot="renderResultStats" slot-scope="renderResultStatsScope">
    @{{ renderResultStatsScope.numberOfResults }} @lang('products')
    <template v-if="renderResultStatsScope.numberOfPages > 1">
        (@lang('page'): @{{ renderResultStatsScope.currentPage + 1 }}/@{{ renderResultStatsScope.numberOfPages }})
    </template>

    <div class="justify-self-end mr-5">
        <div class="flex items-center gap-1 flex-wrap">
            <span class="text-sm text-inactive">@lang('Items per page'):</span>
            <select class="{{ $dropdownClasses }}" v-model="listingScope.pageSize">
                <option
                    v-for="size in $root.config.grid_per_page_values.concat($root.config.translations.all)"
                    v-bind:value="size"
                >
                    @{{ size }}
                </option>
            </select>
        </div>
    </div>
</div>
