<div v-if="images.length > 1" class="flex mt-3 gap-2">
    <button
        v-for="(image, imageId) in images.slice(0, {{ max(config('rapidez.frontend.product_gallery_thumbnails.xs'), config('rapidez.frontend.product_gallery_thumbnails.sm'), config('rapidez.frontend.product_gallery_thumbnails.md'), config('rapidez.frontend.product_gallery_thumbnails.lg'), config('rapidez.frontend.product_gallery_thumbnails.xl')) + 1 }})"
        class="flex items-center justify-center bg-white border rounded p-1.5 aspect-square max-w-24 flex-1 transition-all outline-transparent overflow-hidden relative"
        :class="{
            'outline outline-1 !outline-neutral border-neutral': active == imageId,
            'xl:hidden': imageId > {{ config('rapidez.frontend.product_gallery_thumbnails.xl') }},
            'lg:max-xl:hidden': imageId > {{ config('rapidez.frontend.product_gallery_thumbnails.lg') }},
            'md:max-lg:hidden': imageId > {{ config('rapidez.frontend.product_gallery_thumbnails.md') }},
            'sm:max-md:hidden': imageId > {{ config('rapidez.frontend.product_gallery_thumbnails.sm') }},
            'max-sm:hidden': imageId > {{ config('rapidez.frontend.product_gallery_thumbnails.xs') }}
        }"
        @click="change(imageId)"
    >
        <img
            :src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/magento/catalog/product' + image + '.webp'"
            alt="{{ $product->name }}"
            class="object-contain block w-auto max-h-full"
            width="80"
            height="80"
        />
        <span
            v-if="(imageId + 1) < images.length"
            v-on:click="toggleZoom()"
            class="absolute inset-0 items-center justify-center bg-neutral/20 hidden"
            :class="{
                'xl:!flex': imageId === {{ config('rapidez.frontend.product_gallery_thumbnails.xl') }},
                'lg:max-xl:!flex': imageId === {{ config('rapidez.frontend.product_gallery_thumbnails.lg') }},
                'md:max-lg:!flex': imageId === {{ config('rapidez.frontend.product_gallery_thumbnails.md') }},
                'sm:max-md:!flex': imageId === {{ config('rapidez.frontend.product_gallery_thumbnails.sm') }},
                'max-sm:!flex': imageId === {{ config('rapidez.frontend.product_gallery_thumbnails.xs') }}
            }"
        >
            <span class="size-9 flex items-center justify-center rounded-full shadow-lg bg-white text-sm font-bold text-neutral">
                +@{{ images.length - (imageId + 1) }}
            </span>
        </span>
    </button>
</div>