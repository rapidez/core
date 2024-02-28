<div class="flex gap-5 pb-5 my-2" v-if="resultsType == 'categories'">
    <div class="font-bold">
        @lang('Categories')
    </div>
    <ul class="flex flex-col gap-1">
        <li v-for="hit in resultsData.hits" class="w-full">
            <a :href="hit._source.url" class="w-full hover:text-primary flex gap-1">
                <span class="ml-2" v-html="highlight(hit, 'name')"></span>
            </a>
        </li>
    </ul>
</div>
