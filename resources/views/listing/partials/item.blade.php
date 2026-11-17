<div class="px-5 py-10" data-testid="listing-item">
    <add-to-cart v-bind:product="item" v-slot="addToCart" v-cloak>
        <div class="group relative flex flex-1 flex-col rounded-sm bg-white h-full">
            <a :href="url(addToCart.productUrl)" v-on:click="sendEvent('click', item, 'Hit Clicked')" class="block mb-auto">
                <div class="relative mb-3 h-48 w-full">
                    <img
                        v-if="addToCart.currentThumbnail"
                        :src="url('/storage/{{ config('rapidez.store') }}/resizes/200/magento/catalog/product' + addToCart.currentThumbnail + '.webp')"
                        class="size-full rounded-t-sm object-contain"
                        @if (config('rapidez.frontend.hover_images'))
                            v-bind:class="{ 'group-hover:opacity-0 transition-opacity': addToCart.hoverImage }"
                        @endif
                        :alt="item.name"
                        :loading="config.category && count <= 4 ? 'eager' : 'lazy'"
                        v-bind:style="{ 'view-transition-name': 'image-' + item.sku }"
                        width="200"
                        height="200"
                    />
                    <x-rapidez::no-image v-else="" class="size-full rounded-t-sm" />
                    @if (config('rapidez.frontend.hover_images'))
                        <img
                            v-if="addToCart.hoverImage"
                            :src="url('/storage/{{ config('rapidez.store') }}/resizes/200/magento/catalog/product' + addToCart.hoverImage + '.webp')"
                            class="absolute inset-0 size-full rounded-t-sm object-contain opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"
                            alt=""
                            loading="lazy"
                            width="200"
                            height="200"
                        />
                    @endif
                </div>
                <div>
                    <x-rapidez::highlight attribute="name" class="text-base font-medium hover:underline decoration-2"/>
                    @if (App::providerIsLoaded('Rapidez\Reviews\ReviewsServiceProvider'))
                        <x-dynamic-component
                            component="rapidez-reviews::stars"
                            v-if="item.review_summary?.reviews_count"
                            count="item.review_summary.reviews_count"
                            score="item.review_summary.rating_summary"
                        />
                    @endif
                    <div class="flex items-center gap-x-2 mt-1">
                        <div class="font-semibold text-lg">
                            @{{ price(addToCart.specialPrice || addToCart.price) }}
                        </div>
                        <div class="line-through text-sm" v-if="addToCart.specialPrice">
                            @{{ price(addToCart.price) }}
                        </div>
                    </div>
                </div>
            </a>
            @include('rapidez::listing.partials.item.addtocart')
        </div>
    </add-to-cart>
</div>
