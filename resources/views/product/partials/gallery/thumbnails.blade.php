{{--
    We have two versions of the thumbnails:
    1. PHP version with v-if="false": Shows instantly during page load but is hidden when Vue loads.
    2. Vue version with v-cloak: Takes over when Vue is loaded.

    This prevents layout shifting.
--}}

@php
    $breakpoints = ['xl' => 7, 'lg' => 5, 'md' => 4, 'sm' => 3, 'xs' => 4];
    $baseClasses = 'max-w-24 relative flex aspect-square flex-1 items-center justify-center overflow-hidden rounded border bg-white p-1.5 outline-primary transition-all';
@endphp

<div class="mt-3 flex gap-2">
    @foreach ($product->images as $imageId => $image)
        @if ($imageId < max($breakpoints))
            <button v-if="false" @class([
                $baseClasses,
                'outline outline-1 border-primary' => $imageId == 0,
                'xl:hidden' => $imageId > $breakpoints['xl'],
                'lg:max-xl:hidden' => $imageId > $breakpoints['lg'],
                'md:max-lg:hidden' => $imageId > $breakpoints['md'],
                'sm:max-md:hidden' => $imageId > $breakpoints['sm'],
                'max-sm:hidden' => $imageId > $breakpoints['xs'],
            ])>
                <img
                    src="{{ route('resized-image', [
                        'store' => config('rapidez.store'),
                        'size' => '80x80',
                        'placeholder' => 'magento',
                        'file' => 'catalog/product' . $image,
                        'webp' => '.webp',
                    ]) }}"
                    alt="{{ $product->name }}"
                    class="block max-h-full w-auto object-contain"
                    width="80"
                    height="80"
                />
                @if ($imageId < count($product->images) - 1)
                    <span @class([
                        'absolute inset-0 hidden items-center justify-center bg-black/20',
                        'xl:flex' => $imageId === $breakpoints['xl'],
                        'lg:max-xl:flex' => $imageId === $breakpoints['lg'],
                        'md:max-lg:flex' => $imageId === $breakpoints['md'],
                        'sm:max-md:flex' => $imageId === $breakpoints['sm'],
                        'max-sm:flex' => $imageId === $breakpoints['xs'],
                    ])>
                        <span class="size-9 flex items-center justify-center rounded-full bg-white text-sm font-bold text shadow-lg">
                            +{{ count($product->images) - $imageId - 1 }}
                        </span>
                    </span>
                @endif
            </button>
        @endif
    @endforeach

    <template v-for="(image, imageId) in images.slice(0, {{ max($breakpoints) }})" v-cloak>
        <button
            v-on:click="change(imageId)"
            v-bind:class="{
                'outline outline-1 border-primary': active == imageId,
                'xl:hidden': imageId > {{ $breakpoints['xl'] }},
                'lg:max-xl:hidden': imageId > {{ $breakpoints['lg'] }},
                'md:max-lg:hidden': imageId > {{ $breakpoints['md'] }},
                'sm:max-md:hidden': imageId > {{ $breakpoints['sm'] }},
                'max-sm:hidden': imageId > {{ $breakpoints['xs'] }},
            }"
            class="{{ $baseClasses }}"
        >
            <img
                v-bind:src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/magento/catalog/product' + image + '.webp'"
                alt="{{ $product->name }}"
                class="block max-h-full w-auto object-contain"
                width="80"
                height="80"
            />
            <template v-if="imageId < images.length - 1">
                <span
                    v-on:click="toggleZoom()"
                    v-bind:class="{
                        'xl:flex': imageId === {{ $breakpoints['xl'] }},
                        'lg:max-xl:flex': imageId === {{ $breakpoints['lg'] }},
                        'md:max-lg:flex': imageId === {{ $breakpoints['md'] }},
                        'sm:max-md:flex': imageId === {{ $breakpoints['sm'] }},
                        'max-sm:flex': imageId === {{ $breakpoints['xs'] }}
                    }"
                    class="absolute inset-0 hidden items-center justify-center bg-black/20"
                >
                    <span class="size-9 flex items-center justify-center rounded-full bg-white text-sm font-bold text shadow-lg">
                        +@{{ images.length - imageId - 1 }}
                    </span>
                </span>
            </template>
        </button>
    </template>
</div>
