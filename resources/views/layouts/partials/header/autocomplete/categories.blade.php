<div class="border-b py-4" v-if="resultsType == 'categories'">
    <ul class="flex flex-col font-sans">
        <li v-for="hit in resultsData.hits" class="flex flex-1 items-center w-full">
            <a :href="hit._source.url" class="relative flex items-center group w-full py-2 text-sm gap-x-4">
                @include('rapidez::layouts.partials.header.autocomplete.background')
                <x-heroicon-o-arrow-turn-down-right class="size-5 text-inactive" />
                <span class="ml-2" v-html="highlight(hit, 'name')"></span>
            </a>
        </li>
    </ul>
</div>
