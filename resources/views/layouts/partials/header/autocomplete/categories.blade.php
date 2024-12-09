<div class="border-b pb-2" v-if="resultsType == 'categories'">
    <x-rapidez::autocomplete.title>@lang('Categories')</x-rapidez::autocomplete.title>
    <ul class="flex flex-col font-sans">
        <li v-for="hit in resultsData.hits" :key="hit._source.id" class="flex flex-1 items-center w-full">
            <a :href="hit._source.url" class="relative flex items-center group w-full py-2 text-sm gap-x-4">
                <span class="ml-2 line-clamp-2" v-html="autocompleteScope.highlight(hit, 'name')"></span>
            </a>
        </li>
    </ul>
</div>
