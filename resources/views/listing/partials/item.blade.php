<template {!! isset($slider) ? '' : 'slot="renderItem" slot-scope="{ item, count }"' !!}>
    <div class="flex-none w-1/2 lg:w-1/3 my-0.5 px-0.5 sm:px-2 sm:my-2 snap-start">
        <div class="w-full bg-white border rounded group relative h-full flex flex-col" :key="item.id">
            @if (App::providerIsLoaded('Rapidez\Wishlist\WishlistServiceProvider'))
                <div class="absolute top-0 right-0 z-10 group p-2">
                    @include('rapidez::wishlist.button')
                </div>
            @endif
            <a :href="item.url | url" class="block">
                <picture v-if="item.thumbnail">
                    <source :srcset="'/storage/{{ config('rapidez.store') }}/resizes/200/magento/catalog/product' + item.thumbnail + '.webp'" type="image/webp">
                    <img :src="'/storage/{{ config('rapidez.store') }}/resizes/200/magento/catalog/product' + item.thumbnail" class="object-contain rounded-t h-48 w-full mb-3" :alt="item.name" :loading="config.category && count <= 4 ? 'eager' : 'lazy'" width="200" height="200" />
                </picture>
                <x-rapidez::no-image v-else class="rounded-t h-48 mb-3"/>
                <div class="px-2">
                    <div class="text-base font-medium text-gray-900">@{{ item.name }}</div>
                    @if (!Rapidez::config('catalog/frontend/show_swatches_in_product_list', 1))
                        <div class="flex items-center space-x-2">
                            <div class="font-semibold">@{{ (item.special_price || item.price) | price }}</div>
                            <div class="line-through text-sm" v-if="item.special_price">@{{ item.price | price }}</div>
                        </div>
                    @endif
                </div>
            </a>
            @includeWhen(Rapidez::config('catalog/frontend/show_swatches_in_product_list', 1), 'rapidez::listing.partials.item.addtocart')
        </div>
    </div>
</template>
