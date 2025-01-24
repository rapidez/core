{{-- This component shows an overlay on the last shown thumbnail if there are more thumbnails after it. --}}

<div @attributes([
    'v-cloak' => !(count($product->images) - $imageId - 1),
    'v-if' => 'images.length - ' . $imageId . ' - 1',
])>
    <span
        @class([
            'absolute inset-0 hidden items-center justify-center bg-black/20',
            'xl:flex' => $imageId + 1 === $breakpoints['xl'],
            'lg:max-xl:flex' => $imageId + 1 === $breakpoints['lg'],
            'md:max-lg:flex' => $imageId + 1 === $breakpoints['md'],
            'sm:max-md:flex' => $imageId + 1 === $breakpoints['sm'],
            'max-sm:flex' => $imageId + 1 === $breakpoints['xs'],
        ])
        v-on:click="toggleZoom"
    >
        <span
            class="size-9 flex items-center justify-center rounded-full bg-white text-sm font-bold text shadow-lg"
            v-text="'+' + (images.length - {{ $imageId }} - 1)"
        >
            +{{ count($product->images) - $imageId - 1 }}
        </span>
    </span>
</div>
