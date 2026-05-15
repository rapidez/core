<div v-bind:key="item.entity_id" class="px-5 py-10" data-testid="listing-item">
    <add-to-cart v-bind:product="item" v-slot="addToCart" v-cloak>
        <div class="group flex flex-1 flex-col rounded bg-white h-full">
            <a :href="addToCart.productUrl | url" v-on:click="sendEvent('click', item, 'Hit Clicked')" class="block mb-auto">
                <div class="relative mb-3 h-48 w-full">
                    <img
                        v-if="addToCart.currentThumbnail"
                        :src="'/storage/{{ config('rapidez.store') }}/resizes/200/magento/catalog/product' + addToCart.currentThumbnail + '.webp' | url"
                        class="size-full rounded-t object-contain"
                        v-bind:class="{ 'group-hover:opacity-0 transition-opacity': addToCart.hoverImage }"
                        :alt="item.name"
                        :loading="config.category && count <= 4 ? 'eager' : 'lazy'"
                        v-bind:style="{ 'view-transition-name': 'image-' + item.sku }"
                        width="200"
                        height="200"
                    />
                    <x-rapidez::no-image v-else class="size-full rounded-t" />
                    <img
                        v-if="addToCart.hoverImage"
                        :src="'/storage/{{ config('rapidez.store') }}/resizes/200/magento/catalog/product' + addToCart.hoverImage + '.webp' | url"
                        class="absolute pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity inset-0 size-full rounded-t object-contain"
                        loading="lazy"
                        width="200"
                        height="200"
                    />
                </div>
                <div>
                    <x-rapidez::highlight attribute="name" class="text-base font-medium hover:underline decoration-2"/>
                    @if (App::providerIsLoaded('Rapidez\Reviews\ReviewsServiceProvider'))
                        <x-dynamic-component component="rapidez-reviews::stars" v-if="item.reviews_count" count="item.reviews_count" score="item.reviews_score" />
                    @endif
                    <div class="flex items-center gap-x-2 mt-1">
                        <div class="font-semibold text-lg">
                            @{{ (addToCart.simpleProduct.special_price || addToCart.simpleProduct.price) | price }}
                        </div>
                        <div class="line-through text-sm" v-if="addToCart.simpleProduct.special_price">
                            @{{ addToCart.simpleProduct.price | price }}
                        </div>
                    </div>
                </div>
            </a>
            @include('rapidez::listing.partials.item.addtocart')
        </div>
    </add-to-cart>
</div>
