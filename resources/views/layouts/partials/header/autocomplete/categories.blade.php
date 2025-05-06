<ais-index v-bind:index-name="config.index.category">
    @if ($size = Arr::get($fields, 'size'))
        <ais-configure :hits-per-page.camel="{{ $size }}" />
    @endif
    <ais-hits v-slot="{ items }">
        <div class="border-b p-2" v-if="items && items.length">
            <x-rapidez::autocomplete.title>
                @lang('Categories')
            </x-rapidez::autocomplete.title>
            <ul class="flex flex-col font-sans">
                <li v-for="(item, count) in items" class="flex flex-1 items-center w-full">
                    <a v-bind:href="item.url" class="relative flex items-center group w-full py-2 text-sm gap-x-4">
                        <x-rapidez::highlight attribute="name" class="ml-2 line-clamp-2"/>
                    </a>
                </li>
            </ul>
        </div>
    </ais-hits>
</ais-index>
