<ul class="gap-5 grid md:grid-cols-2">
    <li
        v-for="suggestion in suggestions"
        :key="suggestion.source._id">
        <a :href="suggestion.source.url | url" class="flex flex-wrap flex-1" :key="suggestion.source._id">
            <img :src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/magento/catalog/product' + suggestion.source.thumbnail + '.webp'" class="self-center object-contain w-14 aspect-square" />
            <div class="flex flex-1 flex-wrap px-2">
                <strong class="hyphens block w-full" v-html="highlight(suggestion, 'name')"></strong>
                <div class="self-end">@{{ suggestion.source.price | price }}</div>
            </div>
        </a>
    </li>
</ul>
