<div class="flex-grow text-sm flex justify-between" slot="renderResultStats" slot-scope="{ numberOfResults, numberOfPages, currentPage, time }">
    @{{ numberOfResults }} @lang('products')
    <template v-if="numberOfPages > 1">
        (@lang('page'): @{{ currentPage + 1 }}/@{{ numberOfPages }})
    </template>

    <div class="justify-self-end mr-1">
        <select class="{{ $dropdownClasses }}" v-model="pageSize">
            <option
                v-for="size in $root.config.grid_per_page_values.concat($root.config.translations.all)"
                v-bind:value="size"
            >
                    @{{ size }}
            </option>
        </select>
    </div>
</div>
