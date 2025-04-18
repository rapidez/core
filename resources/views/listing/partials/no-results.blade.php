@lang('Sorry! We did not find any products.')
{{-- TODO: We will want to make this look better --}}
<search-suggestions v-slot="searchSuggestions">
    <ais-instant-search
        v-if="searchSuggestions.searchClient"
        {{-- TODO: Use the new index names --}}
        :index-name="config.index_prefix + '_search_query_' + config.store"
        :search-client="searchSuggestions.searchClient"
    >
        <ais-search-box value="{{ request()->q ?? ' ' }}" />
        <ais-configure :hits-per-page.camel="5"/>
        <ais-hits v-slot="{ items }">
            <div class="mt-1" v-if="items && items.length">
                @lang('But here are some suggestions!')
                <ul class="flex flex-row font-sans">
                    <li v-for="(item, count) in items" class="flex flex-1 items-center w-full">
                        <a v-bind:href="window.url(item.redirect || '{{ route('search', ['q' => 'searchPlaceholder']) }}'.replace('searchPlaceholder', encodeURIComponent(item.query_text)))" class="relative flex items-center group w-full py-2 text-sm gap-x-4">
                            <span v-text="item.query_text"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </ais-hits>
    </ais-instant-search>
</search-suggestions>
