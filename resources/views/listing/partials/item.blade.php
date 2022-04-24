<template {!! isset($slider) ? '' : 'slot="renderItem" slot-scope="{ item, count }"' !!}>
    <div class="flex-none w-1/2 sm:w-1/3 lg:w-1/4 px-1 my-1">
        <div class="w-full bg-white rounded hover:shadow group relative" :key="item.id">
            <a :href="item.url" class="block">
                <picture v-if="item.thumbnail">
                    <source :srcset="'/storage/resizes/200/catalog/product' + item.thumbnail + '.webp'" type="image/webp">
                    <img :src="'/storage/resizes/200/catalog/product' + item.thumbnail" class="object-contain rounded-t h-48 w-full mb-3" :alt="item.name" :loading="config.category && count <= 4 ? 'eager' : 'lazy'" width="200" height="200" />
                </picture>
                <x-rapidez::no-image v-else class="rounded-t h-48 mb-3"/>
                <div class="px-2">
                    <div class="hyphens">@{{ item.name }}</div>
                    @if (!Rapidez::config('catalog/frontend/show_swatches_in_product_list', 1))
                        <div class="flex items-center space-x-2">
                            <div class="font-semibold">@{{ (item.special_price || item.price) | price}}</div>
                            <div class="line-through text-sm" v-if="item.special_price">@{{ item.price | price}}</div>
                        </div>
                    @endif
                </div>
            </a>
            @includeWhen(Rapidez::config('catalog/frontend/show_swatches_in_product_list', 1), 'rapidez::listing.partials.item.addtocart')
        </div>
    </div>
</template>
