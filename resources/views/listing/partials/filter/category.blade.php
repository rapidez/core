<ais-hierarchical-menu
    v-bind:attributes="categoryAttributes"
    v-bind:sort-by="['count','name']"
    @attributes(['root-path' => $rootPath?->join(' > ')])
    show-more
    :limit="6"
>
    <template v-slot="{ items, refine, createURL, isShowingMore, toggleShowMore, canToggleShowMore }">
        <x-rapidez::accordion.filter v-show="items.length" canToggleShowMore>
            <x-slot:label>
                @lang('Category')
            </x-slot:label>
            <x-slot:content>
                <recursion :data="items" v-slot="{ data, components }" class="text-base/5">
                    <ul class="relative before:absolute before:left-0 before:border-l before:bottom-2.5 before:-translate-y-1 before:top-1.5 -my-1">
                        <li class="pl-6 relative before:absolute before:left-0 before:w-3 before:border-b before:top-3 before:translate-y-0.5" v-for="(item, index) in data" :key="item.value">
                            <a
                                class="break-words inline-block py-1 hover:text"
                                :href="createURL(item.value)"
                                :class="{
                                    'font-medium': item.isRefined,
                                    'text-muted': !item.isRefined,
                                }"
                                v-on:click.exact.left.prevent="refine(item.value)"
                            >
                                @{{ item.label }}
                                <span class="text-xs">(@{{ item.count }})</span>
                            </a>

                            <component :is="components[index]" class="mt-1.5 mb-2.5 text-sm/5" />
                        </li>
                    </ul>
                </recursion>
            </x-slot:content>
        </x-rapidez::accordion.filter>
    </template>
</ais-hierarchical-menu>
