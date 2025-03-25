<template {!! isset($slider) ? '' : 'slot="renderItem" slot-scope="{ item, count }"' !!}>
    <div class="size-full">
        <add-to-cart v-bind:product="item" v-slot="addToCart" v-cloak>
            <div class="group relative flex flex-1 flex-col rounded border bg-white p-5" :key="item.entity_id">
                @if (App::providerIsLoaded('Rapidez\Wishlist\WishlistServiceProvider'))
                    <div class="group absolute right-0 top-0 z-10 p-2">
                        @include('rapidez::wishlist.button')
                    </div>
                @endif
                <a :href="item.url | url" class="block">
                    <img
                        v-if="addToCart.currentThumbnail"
                        :src="'/storage/{{ config('rapidez.store') }}/resizes/200/magento/catalog/product' + addToCart.currentThumbnail + '.webp'"
                        class="mb-3 h-48 w-full rounded-t object-contain" :alt="item.name" :loading="config.category && count <= 4 ? 'eager' : 'lazy'"
                        width="200"
                        height="200"
                    />
                    <x-rapidez::no-image v-else class="mb-3 h-48 rounded-t" />
                    <div class="px-2">
                        <div class="text-base font-medium">@{{ item.name }}</div>
                        @if (!Rapidez::config('catalog/frontend/show_swatches_in_product_list'))
                            <div class="flex items-center space-x-2">
                                <div class="font-semibold">@{{ (item.special_price || item.price) | price }}</div>
                                <div class="text-sm line-through" v-if="item.special_price">@{{ item.price | price }}</div>
                            </div>
                        @endif
                        @if (App::providerIsLoaded('Rapidez\Reviews\ReviewsServiceProvider'))
                            <x-dynamic-component component="rapidez-reviews::stars" v-if="item.reviews_count" count="item.reviews_count" score="item.reviews_score" />
                        @endif
                    </div>
                </a>
                @include('rapidez::listing.partials.item.addtocart')
            </div>
        </add-to-cart>
    </div>
</template>
