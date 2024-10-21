<reactive-component
    component-id="category"
    :default-query="() => ({ aggs: {
        categories: { terms: { field: 'categories.keyword' } },
        category_paths: { terms: { field: 'category_paths.keyword' } }
    }})"
    :react="{and: listingScope.reactiveFilters.filter(item => item != 'category') }"
    u-r-l-params
>
    <div slot-scope="categoryFilterComponentSlot">
        <category-filter :aggregations="categoryFilterComponentSlot.aggregations" :value="categoryFilterComponentSlot.value" :set-query="categoryFilterComponentSlot.setQuery">
            <div slot-scope="categoryFilterSlot" class="pb-4">
                <x-rapidez::filter.heading>
                    <x-slot:title>
                        @lang('Category')
                    </x-slot:title>
                    <input class="hidden" type="radio" name="category" value="" :checked="!categoryFilterComponentSlot.value" v-on:change="categoryFilterComponentSlot.setQuery({})"/>
                    <ul>
                        <category-filter-category
                            v-for="category in categoryFilterSlot.results"
                            v-bind:key="category.key"
                            :category="category"
                            :value="(categoryFilterComponentSlot.value || config.category?.entity_id) + ''"
                            :set-query="categoryFilterComponentSlot.setQuery"
                        />
                    </ul>
                </x-rapidez::filter.heading>
            </div>
        </category-filter>
    </div>
</reactive-component>
