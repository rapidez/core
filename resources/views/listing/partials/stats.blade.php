<div class="flex-wrap flex-1 gap-1 text-sm items-center flex justify-between sm:text-base">
    @{{ numberOfResults }} @lang('products')
    <template v-if="numberOfPages > 1">
        (@lang('page'): @{{ currentPage + 1 }}/@{{ numberOfPages }})
    </template>

    <div class="justify-self-end">
        <div class="flex items-center gap-1 flex-wrap">
            <label class="flex items-center gap-x-1.5">
                <x-rapidez::label class="text-sm text-muted whitespace-nowrap mb-0">
                    @lang('Items per page'):
                </x-rapidez::label>

                <ais-hits-per-page :items="listingSlotProps.hitsPerPage">
                    <template v-slot="{ items, refine }">
                        <x-rapidez::input.select class="w-20">
                            <option
                                v-for="item in items"
                                v-bind:key="item.value"
                                v-bind:value="item.value"
                                v-bind:selected="item.isRefined"
                                v-on:change="refine(item.value)"
                            >
                                @{{ item.label }}
                            </option>
                        </x-rapidez::input.select>
                    </template>
                </ais-hits-per-page>
            </label>
        </div>
    </div>
</div>
