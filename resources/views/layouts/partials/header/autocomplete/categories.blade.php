<ais-index v-bind:index-name="config.index.category" v-bind:index-id="'autocomplete_' + config.index.category" class="max-w-2xl w-full mx-auto">
    @if ($size = Arr::get($fields, 'size'))
        <ais-configure :hits-per-page.camel="{{ $size }}" />
    @endif
    <ais-hits v-slot="{ items }">
        <div v-if="items && items.length">
            <x-rapidez::autocomplete.title>
                @lang('Categories')
            </x-rapidez::autocomplete.title>
            <ul class="flex flex-col font-sans">
                <li v-for="(item, count) in items" class="flex flex-1 items-center w-full hover:bg">
                    <a v-bind:href="item.url" class="relative flex items-center group w-full px-5 py-2 text-sm gap-x-1.5">
                        <template v-for="parent in item.parents">
                            <span>@{{ parent }}</span>
                            <x-heroicon-o-chevron-right class="size-3 shrink-0 translate-y-px" />
                        </template>
                        <x-rapidez::highlight attribute="name" class="line-clamp-2"/>
                    </a>
                </li>
            </ul>
        </div>
    </ais-hits>
</ais-index>
