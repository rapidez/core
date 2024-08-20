<ul class="gap-5 flex flex-col py-4">
    <li v-for="suggestion in suggestions" :key="suggestion.source._id">
        <a :href="suggestion.source.url | url" class="group relative flex flex-wrap py-2">
            @include('rapidez::layouts.partials.header.autocomplete.background')
            <img :src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/magento/catalog/product' + suggestion.source.thumbnail + '.webp'" class="shrink-0 self-center object-contain w-14 h-20 mix-blend-multiply" />
            <div class="flex flex-1 justify-center flex-col px-2">
                <strong class="text-sm font-sans hyphens block w-full" v-html="highlight(suggestion, 'name')"></strong>
                <div class="flex items-center gap-x-0.5">
                    <div v-if="suggestion.source.special_price" class="text-inactive font-sans line-through text-xs">
                        @{{ suggestion.source.price | price }}
                    </div>
                    <div class="text-base font-base text-primary font-sans font-bold">
                        @{{ suggestion.source.price | price }}
                    </div>
                </div>
            </div>
        </a>
    </li>
</ul>
