<ais-state-results v-slot="{ status, query }" class="max-w-2xl w-full mx-auto has-[>.ais-Hits:empty]:hidden">
    <div v-if="status === 'stalled'" class="flex items-center mx-auto px-5 py-2.5">
        <x-rapidez-loading class="size-5 text-gray-200 animate-spin fill-primary" />
        <span class="ml-2">@lang('Searching...')</span>
    </div>
    <ais-hits>
        <template v-slot="{ items }">
            <div v-if="items.length === 0 && query !== '' && status === 'idle'"  class="p-5 rounded border border-muted">
                <div class="font-bold text text-lg break-all">
                    @lang('No results found for :searchterm', [
                        'searchterm' => '<span class="text-primary">"@{{ query }}"</span>'
                    ])
                </div>
                <div class="flex flex-col text-sm pt-7">
                    <span class="font-bold">@lang('Have you tried:')</span>
                    <ul class="flex flex-col pt-1.5 gap-y-1 *:flex *:gap-x-2 *:items-center">
                        <li>
                            <x-heroicon-s-check class="size-4"/>
                            @lang('Check the spelling of your search term')
                        </li>
                        <li>
                            <x-heroicon-s-check class="size-4"/>
                            @lang('Make your search term less specific')
                        </li>
                        <li>
                            <x-heroicon-s-check class="size-4"/>
                            @lang('Use other search terms')
                        </li>
                    </ul>
                </div>
            </div>
        </template>
    </ais-hits>
</ais-state-results>
