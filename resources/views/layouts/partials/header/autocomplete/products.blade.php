<ais-infinite-hits v-bind:index-name="config.index.product" v-bind:index-id="'autocomplete_' + config.index.product" v-slot="{ items, sendEvent, isLastPage, refineNext }">
    <div>
        <div v-if="items && items.length">
            <div class="mb-5 flex max-md:flex-col-reverse gap-y-3 md:items-center justify-between">
                <p class="font-medium text-2xl inline-block">
                    <ais-state-results v-slot="{ query }">
                        <template v-if="query && query !== '__NO_QUERY__'">
                            @lang('Search for'): @{{ query }}
                        </template>
                        <template v-else>
                            @lang('Search')
                        </template>
                    </ais-state-results>
                </p>

                @include('rapidez::layouts.partials.header.autocomplete.all-results')
            </div>

            <div class="overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 -mx-5 -mb-px *:border-b">
                    <template v-for="(item, count) in items">
                        @include('rapidez::listing.partials.item')
                    </template>
                    <div v-if="!isLastPage" v-intersection-observer="([entry]) => entry?.isIntersecting ? refineNext() : ''"></div>
                </div>
                <ais-state-results v-slot="{ status }" v-if="!isLastPage">
                    <div
                        class="flex pt-4"
                    >
                        <x-rapidez::button class="mx-auto" v-bind:disabed="status !== 'idle'" v-on:click="() => status === 'idle' && refineNext()">
                            <span v-if="status === 'idle'">@lang('Show more')</span>
                            <span v-else>@lang('Loading')...</span>
                        </x-rapidez::button>
                    </div>
                </ais-state-results>
            </div>
        </div>
    </div>
</ais-infinite-hits>
