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
                    <div class="text-neutral font-semibold text-base font-sans">
                        @lang('You have filtered for:')
                    </div>
                    <button v-on:click="clearValues" class="!font-sans text-sm text-inactive transition-all hover:underline">
                        @lang('Reset filters')
                    </button>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <div class="flex flex-wrap gap-2 relative cursor-pointer" v-for="filter in activeFilters">
                        <div v-on:click="setValue(filter.code, null)" class="flex justify-between items-center transition hover:opacity-80">
                            <span class="font-sans flex gap-1 p-1 items-center text-xs test-neutral rounded-lg bg-inactive-100">
                                @{{ filter.value }}
                                <x-heroicon-o-x-mark class="size-3 shrink-0 text-neutral"/>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </selected-filters-values>
    </div>
</selected-filters>
