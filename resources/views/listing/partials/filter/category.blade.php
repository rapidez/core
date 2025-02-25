<ais-hierarchical-menu
    :attributes="[
        'category_lvl1',
        'category_lvl2',
        'category_lvl3',
        {{-- TODO: This should be dynamic up to the max depth --}}
    ]"
    {{-- root-path="Men > Tops" --}}
    show-more
>
    <template v-slot="{ items, refine, createURL }">
        {{-- See the HierarchicalMenuList.vue --}}
        <recursion :data="items" v-slot="{ data, components }">
            <ul>
                <li class="pl-3" v-for="(item, index) in data" :key="item.value">
                    <a
                        :href="createURL(item.value)"
                        :class="{ 'font-bold': item.isRefined }"
                        v-on:click.exact.left.prevent="refine(item.value)"
                    >
                        @{{ item.label }}
                        (@{{ item.count }})
                    </a>

                    <component :is="components[index]" />
                </li>
            </ul>
        </recursion>
    </template>
</ais-hierarchical-menu>

{{--
<reactive-component
    component-id="category"
    :default-query="() => ({ aggs: {
        categories: { terms: { field: 'categories.keyword' } },
        category_paths: { terms: { field: 'category_paths.keyword' } }
    }})"
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
 --}}
