<ais-clear-refinements v-bind:excluded-attributes="[...categoryAttributes, 'query']">
    <template v-slot="{ canRefine, refine, createURL }">
        <div v-show="canRefine" class="flex flex-wrap items-baseline justify-between gap-2 w-full pb-2.5">
            <div class="text-sm">
                @lang('Selected filters'):
            </div>
            <a
                v-bind:href="createURL()"
                v-if="canRefine"
                v-on:click.prevent="refine"
                class="text-sm text-muted underline hover:text"
            >
                @lang('Reset filters')
            </a>
        </div>
    </template>
</ais-clear-refinements>

<ais-current-refinements>
    <template v-slot="{ items, createURL }">
        <ul class="flex gap-x-1.5 gap-y-2 flex-wrap has-[li]:pb-4">
            <template v-for="item in withFilters(items)">
                <li
                    class="flex flex-wrap relative"
                    v-for="refinement in withSwatches(item.refinements, item.filter)"
                    :key="[
                      refinement.attribute,
                      refinement.type,
                      refinement.value,
                      refinement.operator
                    ].join(':')"
                >
                    <a
                        v-bind:href="createURL(refinement)"
                        v-on:click.prevent="item.refine(refinement)"
                        class="flex items-center gap-1 py-2 px-3 text-sm rounded-full bg hover:bg-emphasis focus:outline-emphasis"
                    >
                        <x-heroicon-o-x-mark class="size-3.5 shrink-0 stroke-2 text-muted"/>
                        @{{ item.filter.name }}:
                        <template v-if="false"></template>
                        @include('rapidez::listing.partials.filter.selected.boolean')
                        @include('rapidez::listing.partials.filter.selected.swatch')
                        <template v-else>
                            @{{ refinement.label }}
                        </template>
                    </a>
                </li>
            </template>
        </ul>
    </template>
</ais-current-refinements>
