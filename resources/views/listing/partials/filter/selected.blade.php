<selected-filters :inner-class="{ button: '!hidden last:!inline-flex' }">
    <x-rapidez::button
        class="w-full mb-3 text-sm"
        slot-scope="selectedFiltersScope"
        v-on:click="selectedFiltersScope.clearValues"
        v-if="Object.keys(selectedFiltersScope.selectedValues).filter(function (id) {
            let value = selectedFiltersScope.selectedValues[id].value
            let isArray = Array.isArray(value)

            return selectedFiltersScope.components.includes(id)
                && selectedFiltersScope.selectedValues[id].showFilter
                && (
                    (isArray && value.length)
                    || (!isArray && value)
                )
        }).length"
    >
        @lang('Reset filters')
    </x-rapidez::button>
</selected-filters>
