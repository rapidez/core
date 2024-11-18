<template {!! isset($slider) ? '' : 'slot="renderItem" slot-scope="{ item, count }"' !!}>
    <div class="my-0.5 w-full h-full shrink-0 snap-start px-0.5 sm:my-2 sm:px-2 sm:w-1/2 lg:w-1/3">
        <div class="group relative flex flex-1 flex-col rounded border bg-white p-5" :key="item.entity_id">
            @if (App::providerIsLoaded('Rapidez\Wishlist\WishlistServiceProvider'))
                <div class="group absolute right-0 top-0 z-10 p-2">
                    @include('rapidez::wishlist.button')
                </div>
            @endif
            <a :href="item.url | url" class="block">
                <img
                    v-if="item.thumbnail"
                    :src="resizedPath(item.thumbnail + '.webp', '200')"
                    class="mb-3 h-48 w-full rounded-t object-contain" :alt="item.name" :loading="config.category && count <= 4 ? 'eager' : 'lazy'"
                    width="200"
                    height="200"
                />
                <x-rapidez::no-image v-else class="mb-3 h-48 rounded-t" />
                <div class="px-2">
                    <div class="text-base font-medium text-neutral">@{{ item.name }}</div>
                    @if (!Rapidez::config('catalog/frontend/show_swatches_in_product_list', 1))
                        <div class="flex items-center space-x-2">
                            <div class="font-semibold">@{{ (item.special_price || item.price) | price }}</div>
                            <div class="text-sm line-through" v-if="item.special_price">@{{ item.price | price }}</div>
                        </div>
                    @endif
                </div>
            </a>
            @includeWhen(Rapidez::config('catalog/frontend/show_swatches_in_product_list', 1), 'rapidez::listing.partials.item.addtocart')
        </div>
    </div>
</template>
