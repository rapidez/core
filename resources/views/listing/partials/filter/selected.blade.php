<ais-clear-refinements>
    <template v-slot="{ canRefine, refine, createURL }">
        <a
            v-if="canRefine"
            v-bind:href="createURL()"
            v-on:click.prevent="refine"
        >
            @lang('Reset filters')
        </a>

        {{-- TODO: Without this the default renders... --}}
        <div v-else></div>
    </template>
</ais-clear-refinements>

<ais-current-refinements>
    <template v-slot="{ items, createURL }">
        <ul class="flex gap-2 flex-wrap">
            <template v-for="item in withFilters(items)">
                <li
                    class="flex flex-wrap gap-2 relative"
                    v-for="refinement in item.refinements"
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
                        class="flex justify-between items-center transition hover:opacity-80"
                    >
                        {{-- Why do we need an extra span here? --}}
                        <span class="font-sans flex gap-1 p-1 items-center text-xs rounded-lg bg">
                            {{--
                            Having the label here is useful when filtering booleans,
                            but the item label is currently the "code".
                            --}}

                            @{{ item.label }}:
                            <template v-if="item.filter.input === 'boolean'">
                                @{{ refinement.label == 1 ? 'Yes' : 'No' }}
                            </template>
                            <template v-else>
                                @{{ refinement.label }}
                            </template>
                            <x-heroicon-o-x-mark class="size-3 shrink-0"/>
                        </span>
                    </a>
                </li>
            </template>
        </ul>
    </template>
</ais-current-refinements>

{{--
TODO: Make sure all of this is implemented

<selected-filters>
    <div
        slot-scope="{ clearValues, selectedValues, setValue, components }"
        v-if="Object.keys(selectedValues).filter(function (id) {
            let value = selectedValues[id].value
            let isArray = Array.isArray(value)
            return components.includes(id) && selectedValues[id].showFilter && ((isArray && value.length) || (!isArray && value))}).length"
    >
        <selected-filters-values :selected-values="selectedValues">
            <div slot-scope="{ activeFilters }" class="flex flex-wrap items-center w-full md:w-auto relative mb-5">
                <div class="flex flex-wrap items-baseline justify-between gap-2 w-full border-t py-4">
                    <div class="font-semibold text-base font-sans">
                        @lang('You have filtered for:')
                    </div>
                    <button v-on:click="clearValues" class="!font-sans text-sm text-muted transition-all hover:underline">
                        @lang('Reset filters')
                    </button>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <div class="flex flex-wrap gap-2 relative cursor-pointer" v-for="filter in activeFilters">
                        <div v-on:click="setValue(filter.code, null)" class="flex justify-between items-center transition hover:opacity-80">
                            <span class="font-sans flex gap-1 p-1 items-center text-xs rounded-lg bg">
                                @{{ filter.value }}
                                <x-heroicon-o-x-mark class="size-3 shrink-0"/>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </selected-filters-values>
    </div>
</selected-filters>
 --}}
