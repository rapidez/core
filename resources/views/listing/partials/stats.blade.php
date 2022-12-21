<div class="flex-grow text-base flex justify-between" slot="renderResultStats" slot-scope="{ numberOfResults, numberOfPages, currentPage, time }">
    @{{ numberOfResults }} @lang('products')
    <template v-if="numberOfPages > 1">
        (@lang('page'): @{{ currentPage + 1 }}/@{{ numberOfPages }})
    </template>

    <div class="justify-self-end mr-1">
        <select class="{{ $dropdownClasses }}" v-model="$root.config.grid_per_page">
            <option v-for="s in $root.config.grid_per_page_values.concat($root.config.translations.all)" v-bind:value="s"> @{{ s }}</option>
        </select>
    </div>
</div>
