<ais-hits>
    <template v-slot="{ items }">
        <div v-if="items && items.length">
            <x-rapidez::button.outline
                type="submit"
                form="autocomplete-form"
            >
                <span>@lang('View all products')</span>
            </x-rapidez::button.outline>
        </div>
    </template>
</ais-hits>
