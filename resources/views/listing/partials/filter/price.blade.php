<div v-if="filter.input == 'price'" class="relative pb-4">
    <x-rapidez::filter.heading>
        <dynamic-range-slider
            :component-id="filter.code"
            :data-field="filter.code"
            :react="{and: ['query-filter']}"
            :slider-options="{ dragOnClick: true, useKeyboard: false }"
            :inner-class="{
                slider: '!pt-4 !mt-0 mx-2',
            }"
            u-r-l-params
        >
        </dynamic-range-slider>
    </x-rapidez::filter.heading>
</div>
