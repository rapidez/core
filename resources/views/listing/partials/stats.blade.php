<div class="flex-wrap flex-1 gap-1 text-sm items-center flex justify-between sm:text-base" slot="renderResultStats" slot-scope="{ numberOfResults, numberOfPages, currentPage, time }">
    @{{ numberOfResults }} @lang('products')
    <template v-if="numberOfPages > 1">
        (@lang('page'): @{{ currentPage + 1 }}/@{{ numberOfPages }})
    </template>

    <div class="justify-self-end">
        <div class="flex items-center gap-1 flex-wrap">
            <label class="flex items-center gap-x-1.5">
                <x-rapidez::label class="text-sm text-muted whitespace-nowrap mb-0">@lang('Items per page'):</x-rapidez::label>
                <x-rapidez::input.select v-model="listingSlotProps.pageSize" class="w-20">
                    <option
                        v-for="size in $root.config.grid_per_page_values.concat($root.config.translations.all)"
                        v-bind:value="size"
                    >
                        @{{ size }}
                    </option>
                </x-rapidez::input.select>
            </label>
        </div>
    </div>
</div>
