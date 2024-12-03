<div class="flex-wrap flex-1 gap-1 text-base flex justify-between" slot="renderResultStats" slot-scope="{ numberOfResults, numberOfPages, currentPage, time }">
    @{{ numberOfResults }} @lang('products')
    <template v-if="numberOfPages > 1">
        (@lang('page'): @{{ currentPage + 1 }}/@{{ numberOfPages }})
    </template>

    <div class="justify-self-end mr-5">
        <div class="flex items-center gap-1 flex-wrap">
            <span class="text-sm text-muted">@lang('Items per page'):</span>
            <select class="{{ $dropdownClasses }}" v-model="listingSlotProps.pageSize">
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
