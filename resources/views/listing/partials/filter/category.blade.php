<reactive-component
    component-id="category"
    :default-query="() => ({
        aggs: {
            categories: {
                terms: {
                    field: 'categories.keyword'
                }
            },
            category_paths: {
                terms: {
                    field: 'category_paths.keyword'
                }
            }
        }
    })"
    :react="{and: reactiveFilters.filter(item => item != 'category') }"
    u-r-l-params
>
    <div slot-scope="{ aggregations, setQuery, value }">
        <category-filter :aggregations="aggregations" :current-category="config.category" :value="value" :set-query="setQuery">
            <div slot-scope="{ hasResults, results }">
                <strong v-if="hasResults">@lang('Category')</strong>
                <input class="hidden" type="radio" name="category" value="" :checked="!value" v-on:change="setQuery({})"/>
                <label class="block" v-for="category in results" v-bind:key="category.key">
                    <input type="radio" name="category" :value="category.key" v-on:change="setQuery({
                        query: { term: { 'categories.keyword': category.key } },
                        value: category.key
                    })"/>
                    {{-- TODO: We need some kind of tree here --}}
                    <span>
                        @{{ category.label }}
                    </span>
                    <small>(@{{ category.doc_count }})</small>
                </label>
            </div>
        </category-filter>
    </div>
</reactive-component>
