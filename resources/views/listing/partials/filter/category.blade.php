<reactive-component
    component-id="category"
    :default-query="() => ({
        aggs: {
            categories: {
                terms: {
                    field: 'categories.keyword'
                }
            }
        }
    })"
    :react="{and: reactiveFilters.filter(item => item != 'category') }"
    u-r-l-params
>
    <div slot-scope="{ aggregations, setQuery, value }">
        <category-filter :aggregations="aggregations">
            <div slot-scope="{ hasResults }">
                <strong v-if="hasResults">@lang('Category')</strong>
                <a href="#" class="flex justify-between" v-for="category in aggregations.categories.buckets" v-on:click.prevent="setQuery({
                    query: { term: { 'categories.keyword': category.key } },
                    value: category.key
                })" v-if="(config.category && category.key.startsWith(config.category.pathWithNames)) || !config.category">
                    {{-- TODO: We need some kind of tree here --}}
                    <span v-if="config.category">
                        @{{ category.key.replace(config.category.pathWithNames+' /// ', '').replace('///', '/') }}
                    </span>
                    <span v-else>@{{ category.key.replace('///', '/') }}</span>
                    <small>(@{{ category.doc_count }})</small>
                </a>
            </div>
        </category-filter>
    </div>
</reactive-component>
