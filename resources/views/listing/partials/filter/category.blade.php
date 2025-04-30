<ais-hierarchical-menu
    v-bind:attributes="categoryAttributes"
    @attributes(['root-path' => $rootPath])
    show-more
    :limit="6"
>
    <template v-slot="{ items, refine, createURL, isShowingMore, toggleShowMore, canToggleShowMore }">
        <div v-show="items.length" class="relative pb-5">
            <x-rapidez::filter.heading>
                <x-slot:title>
                    @lang('Category')
                </x-slot:title>
                <recursion :data="items" v-slot="{ data, components }" class="text-base/5">
                    <ul class="relative before:absolute before:left-0 before:border-l before:bottom-2.5 before:-translate-y-1 before:top-1.5 -my-1">
                        <li class="pl-6 relative before:absolute before:left-0 before:w-3 before:border-b before:top-3 before:translate-y-0.5" v-for="(item, index) in data" :key="item.value">
                            <a
                                class="hover:text inline-block py-1"
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
            </x-rapidez::filter.heading>

            <button
                v-if="canToggleShowMore"
                v-on:click="toggleShowMore"
                class="text-sm text-primary font-medium mt-3 hover:underline"
            >
                <span v-if="isShowingMore" class="flex gap-x-4">
                    @lang('Less options')
                </span>
                <span v-else class="flex gap-x-4">
                    @lang('More options')
                </span>
            </button>
        </div>
    </template>
</ais-hierarchical-menu>
