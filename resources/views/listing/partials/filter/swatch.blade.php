<multi-list
    v-else-if="filter.text_swatch || filter.visual_swatch"
    :component-id="filter.code"
    :data-field="filter.super ? 'super_' + filter.code : (filter.visual_swatch ? 'visual_' + filter.code : filter.code)"
    :inner-class="{
        title: 'capitalize text-sm font-medium text',
    }"
    :react="{and: filter.input == 'multiselect' ? reactiveFilters : reactiveFilters.filter(item => item != filter.code) }"
    :query-format="filter.input == 'multiselect' ? 'and' : 'or'"
    :show-search="false"
    :show-checkbox="false"
    u-r-l-params
>
    <div slot="render" class="pb-4" slot-scope="{ data, value, handleChange }">
        <x-rapidez::filter.heading>
            <div class="flex flex-wrap gap-2 items-center my-1">
                <template v-for="swatch in data">
                    <label
                        v-if="filter.visual_swatch"
                        class="size-6 ring-emphasis/10 ring-1 ring-inset cursor-pointer flex items-center justify-center hover:opacity-75 rounded-full transition"
                        v-bind:class="{'outline-2 outline outline-emphasis outline-offset-1' : value[swatch.key]}"
                        v-bind:style="{ background: $root.swatches[filter.code]?.options[swatch.key].swatch }"
                    >
                        <input type="checkbox" v-on:change="handleChange(swatch.key)" class="hidden" v-bind:checked="value[swatch.key]"/>
                    </label>
                    <label
                        v-else
                        class="border px-3 transition-all rounded cursor-pointer text-sm text-muted font-medium"
                        v-bind:class="{'border text' : value[swatch.key]}"
                    >
                        @{{ $root.swatches[filter.code]?.options[swatch.key].swatch }}
                        <input type="checkbox" v-on:change="handleChange(swatch.key)" class="hidden" v-bind:checked="value[swatch.key]"/>
                    </label>
                </template>
            </div>
        </x-rapidez::filter.heading>
    </div>
</multi-list>
