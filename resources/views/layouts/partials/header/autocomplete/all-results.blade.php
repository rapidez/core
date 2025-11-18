<ais-hits>
    <template v-slot="{ items }">
        <div v-if="items && items.length" class="font-sans px-5 py-2.5">
            <x-rapidez::button.primary
                type="submit"
                form="autocomplete-form"
                class="relative group flex items-center gap-x-4 w-full text-sm"
            >
                <span>@lang('View all products')</span>
            </x-rapidez::button.primary>
        </div>
    </template>
</ais-hits>
