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
            <div slot-scope="{ hasResults, results }">
                <strong v-if="hasResults">@lang('Category')</strong>
                <input class="hidden" type="radio" name="category" value="" :checked="!value" v-on:change="setQuery({})"/>
                <ul class="mb-5">
                    <category-filter-category
                        v-for="category in results"
                        v-bind:key="category.key"
                        :category="category"
                        :value="(value || config.category?.entity_id) + ''"
                        :set-query="setQuery"
                    />
                </ul>
            </div>
        </category-filter>
    </div>
</reactive-component>
