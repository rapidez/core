@php($breakpoints = ['xl' => 7, 'lg' => 5, 'md' => 4, 'sm' => 3, 'xs' => 4])
<div v-if="images.length > 1" class="flex mt-3 gap-2">
    <button
        v-for="(image, imageId) in images.slice(0, {{ max($breakpoints) }})"
        class="flex items-center justify-center bg-white border rounded p-1.5 aspect-square max-w-24 flex-1 transition-all outline-transparent overflow-hidden relative"
        :class="{
            'outline outline-1 !outline-neutral border-neutral': active == imageId,
            'xl:hidden': imageId > {{ $breakpoints['xl'] }},
            'lg:max-xl:hidden': imageId > {{ $breakpoints['lg'] }},
            'md:max-lg:hidden': imageId > {{ $breakpoints['md'] }},
            'sm:max-md:hidden': imageId > {{ $breakpoints['sm'] }},
            'max-sm:hidden': imageId > {{ $breakpoints['xs'] }}
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
                'xl:flex': imageId === {{ $breakpoints['xl'] }},
                'lg:max-xl:flex': imageId === {{ $breakpoints['lg'] }},
                'md:max-lg:flex': imageId === {{ $breakpoints['md'] }},
                'sm:max-md:flex': imageId === {{ $breakpoints['sm'] }},
                'max-sm:flex': imageId === {{ $breakpoints['xs'] }}
            }"
        >
            <span class="size-9 flex items-center justify-center rounded-full shadow-lg bg-white text-sm font-bold text-neutral">
                +@{{ images.length - (imageId + 1) }}
            </span>
        </span>
    </button>
</div>
