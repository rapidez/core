<div class="border py-4 sm:py-7">
    <h2 class="font-sans font-bold text-2xl text px-4 sm:px-7">@lang('Sorry! We did not find any products.')</h2>
    <search-suggestions v-slot="searchSuggestions">
        <ais-instant-search
            v-if="searchSuggestions.searchClient"
            :index-name="config.index.search_query"
            :search-client="searchSuggestions.searchClient"
        >
            <ais-search-box value="{{ request()->q ?? ' ' }}" />
            <ais-configure :hits-per-page.camel="5"/>
            <ais-hits v-slot="{ items }">
                <div class="mt-3" v-if="items && items.length">
                    <div class="px-4 sm:px-7">
                        <div class="font-sans text-sm text mb-2">@lang('But here are some suggestions:')</div>
                        <ul class="flex flex-col font-sans">
                            <li v-for="(item, count) in items" class="flex flex-1 items-center w-full">
                                <a
                                    v-bind:href="window.url(item.redirect || '{{ route('search', ['q' => 'searchPlaceholder']) }}'.replace('searchPlaceholder', encodeURIComponent(item.query_text)))"
                                    class="flex items-center group py-1 text-sm gap-x-0.5 hover:underline"
                                >
                                    <x-heroicon-o-chevron-right class="size-3.5 shrink-0"/> <span v-text="item.query_text"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-4 border-t">
                        <div class="px-4 sm:px-7">
                            <a href="/" class="flex items-center group mt-3 py-1 text-sm gap-x-0.5 hover:underline" aria-label="@lang('Go to home')">
                                <x-heroicon-o-chevron-right class="size-3.5 shrink-0"/> <span>@lang('Go to home')</span>
                            </a>
                        </div>
                    </div>
                </div>
            </ais-hits>
        </ais-instant-search>
    </search-suggestions>
</div>
