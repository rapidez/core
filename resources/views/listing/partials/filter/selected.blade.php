<selected-filters :inner-class="{ button: '!hidden last:!inline-flex' }">
    <x-rapidez::button
        class="w-full mb-3 text-sm"
        slot-scope="{ clearValues, selectedValues, components }"
        v-on:click="clearValues"
        v-if="Object.keys(selectedValues).filter(function (id) {
            let value = selectedValues[id].value
            let isArray = Array.isArray(value)

            return components.includes(id)
                && selectedValues[id].showFilter
                && (
                    (isArray && value.length)
                    || (!isArray && value)
                )
        }).length"
    >
        @lang('Reset filters')
    </x-rapidez::button>
</selected-filters>
