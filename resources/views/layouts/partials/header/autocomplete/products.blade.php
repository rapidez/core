<div class="pt-4 pb-2">
    <x-rapidez::autocomplete.title>@lang('Products')</x-rapidez::autocomplete.title>
    <ul class="gap-2 flex flex-col">
        <li v-for="suggestion in suggestions" :key="suggestion.source._id">
            <a :href="suggestion.source.url | url" class="group relative flex flex-wrap py-2">
                <img :src="'/storage/{{ config('rapidez.store') }}/resizes/200/magento/catalog/product' + suggestion.source.thumbnail + '.webp'" class="shrink-0 self-center object-contain size-16 mix-blend-multiply" />
                <div class="flex flex-1 justify-center flex-col px-2">
                    <div class="text-sm font-medium font-sans hyphens-auto line-clamp-2 w-full" v-html="highlight(suggestion, 'name')"></div>
                    <div class="flex items-center gap-x-0.5 mt-0.5">
                        <div v-if="suggestion.source.special_price" class="text-muted font-sans line-through text-xs">
                            @{{ suggestion.source.price | price }}
                        </div>
                        <div class="text-sm text font-sans font-bold">
                            @{{ (suggestion.source.special_price || suggestion.source.price) | price }}
                        </div>
                    </div>
                </div>
            </a>
        </li>
    </ul>
</div>
