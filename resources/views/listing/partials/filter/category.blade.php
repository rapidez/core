<ais-hierarchical-menu
    v-bind:attributes="categoryAttributes"
    @attributes(['root-path' => $rootPath])
    show-more
>
    <template v-slot="{ items, refine, createURL }">
        <div v-show="items.length">
            <x-rapidez::filter.heading>
                <x-slot:title>
                    @lang('Category')
                </x-slot:title>
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
            </x-rapidez::filter.heading>
        </div>
    </template>
</ais-hierarchical-menu>
