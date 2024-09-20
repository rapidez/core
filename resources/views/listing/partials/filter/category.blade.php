<reactive-component
    component-id="category"
    :default-query="() => ({ aggs: {
        categories: { terms: { field: 'categories.keyword' } },
        category_paths: { terms: { field: 'category_paths.keyword' } }
    }})"
    :react="{and: reactiveFilters.filter(item => item != 'category') }"
    u-r-l-params
>
    <div slot-scope="{ aggregations, setQuery, value }">
        <category-filter :aggregations="aggregations" :value="value" :set-query="setQuery">
            <div slot-scope="{ hasResults, results }" class="pb-4">
                <x-rapidez::filter.heading>
                    <x-slot:title>
                        @lang('Category')
                    </x-slot:title>
                    <input class="hidden" type="radio" name="category" value="" :checked="!value" v-on:change="setQuery({})"/>
                    <ul>
                        <category-filter-category
                            v-for="category in results"
                            v-bind:key="category.key"
                            :category="category"
                            :value="(value || config.category?.entity_id) + ''"
                            :set-query="setQuery"
                        />
                    </ul>
                </x-rapidez::filter.heading>
            </div>
        </category-filter>
    </div>
</reactive-component>
