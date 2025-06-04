<ais-index v-bind:index-name="config.index.category" v-bind:index-id="'autocomplete_' + config.index.category">
    @if ($size = Arr::get($fields, 'size'))
        <ais-configure :hits-per-page.camel="{{ $size }}" />
    @endif
    <ais-hits v-slot="{ items }">
        <div class="border-b py-2.5" v-if="items && items.length">
            <x-rapidez::autocomplete.title>
                @lang('Categories')
            </x-rapidez::autocomplete.title>
            <ul class="flex flex-col font-sans">
                <li v-for="(item, count) in items" class="flex flex-1 items-center w-full hover:bg-muted">
                    <a v-bind:href="item.url" class="relative flex items-center group w-full px-5 py-2 text-sm gap-x-2">
                        <template v-for="parent in item.parents.slice(0,-1)">
                            <span>@{{ parent }}</span>
                            <span>&gt;</span>
                        </template>
                        <x-rapidez::highlight attribute="name" class="line-clamp-2"/>
                    </a>
                </li>
            </ul>
        </div>
    </ais-hits>
</ais-index>
