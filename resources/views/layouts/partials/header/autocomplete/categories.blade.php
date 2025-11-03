<ais-index v-bind:index-name="config.index.category" v-bind:index-id="'autocomplete_' + config.index.category" class="max-w-2xl w-full mx-auto">
    <div>
        @if ($size = Arr::get($fields, 'size'))
            <ais-configure :hits-per-page.camel="{{ $size }}" />
        @endif
        <ais-hits v-slot="{ items }">
            <div>
                <div v-if="items && items.length">
                    <x-rapidez::autocomplete.title>
                        @lang('Categories')
                    </x-rapidez::autocomplete.title>
                    <ul class="flex flex-col font-sans">
                        <li v-for="(item, count) in items" class="flex flex-1 items-center w-full hover:bg rounded">
                            <a v-bind:href="item.url" class="relative flex items-center group w-full sm:px-5 py-1.5 gap-x-2.5 text-sm">
                                <x-heroicon-o-magnifying-glass class="size-5 text-muted" />

                                <span>
                                    <x-rapidez::highlight attribute="name" class="line-clamp-2 inline" />

                                    <span class="text-muted inline" v-if="item.parents && item.parents.length">
                                        @lang('in')
                                        <template v-for="parent in item.parents">
                                            <span class="lowercase"> @{{ parent }}</span>
                                        </template>
                                    </span>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
                @if ($defaultValues = Arr::get($fields, 'defaultValues'))
                    <div v-else>
                        <x-rapidez::autocomplete.title>
                            @lang('Categories')
                        </x-rapidez::autocomplete.title>
                        <ul class="flex flex-col font-sans">
                            @foreach (collect(value($defaultValues))->take(Arr::get($fields, 'size', config('rapidez.frontend.autocomplete.size', 3))) as $category)
                                <li class="flex flex-1 items-center w-full hover:bg rounded">
                                    <a href="{{ $category['url'] }}" class="relative flex items-center group w-full sm:px-5 py-1.5 gap-x-2.5 text-sm">
                                        <x-heroicon-o-magnifying-glass class="size-5 text-muted" />

                                        {{ $category['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </ais-hits>
    </div>
</ais-index>
