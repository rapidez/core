{{--
This components initializes with data from PHP to avoid layout shifts.
When Vue is loaded it takes over and all `v-cloak`'s show up.
With `$breakpoints` you can control the amount of images.
--}}

@php($breakpoints = ['xl' => 7, 'lg' => 5, 'md' => 7, 'sm' => 5, 'xs' => 3])

<div class="mt-3 flex gap-2">
    @for ($imageId = 0; $imageId < max($breakpoints); $imageId++)
        <button
            @attributes([
                'v-cloak' => !($imageId < count($product->images) && count($product->images) > 1),
                'v-if' => $imageId . ' < images.length && images.length > 1',
            ])
            @class([
                'max-w-24 relative flex aspect-square flex-1 items-center justify-center overflow-hidden rounded-sm border bg-white p-1.5 outline-primary transition-all',
                'outline outline-1 border-primary' => $imageId == 0,
                'xl:hidden' => $imageId >= $breakpoints['xl'],
                'lg:max-xl:hidden' => $imageId >= $breakpoints['lg'],
                'md:max-lg:hidden' => $imageId >= $breakpoints['md'],
                'sm:max-md:hidden' => $imageId >= $breakpoints['sm'],
                'max-sm:hidden' => $imageId >= $breakpoints['xs'],
            ])
            v-bind:class="{
                'outline outline-1 border-primary': active === {{ $imageId }},
                'outline-transparent! outline-0! border-border!': active !== {{ $imageId }},
            }"
            v-on:click="change({{ $imageId }})"
        >
            <img
                {{-- src should always be above v-bind:src --}}
                @if ($imageId < count($product->images))
                    src="/storage/{{ config('rapidez.store') }}/resizes/80x80/magento/catalog/product/{{ $product->images[$imageId] }}.webp"
                @endif
                v-bind:src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/magento/catalog/product/' + images[{{ $imageId }}] + '.webp'"
                alt="{{ $product->name }}"
                class="block max-h-full w-auto object-contain"
                width="80"
                height="80"
            />

            {{-- Only include the showMore if it's actually possible to be visible --}}
            @includeWhen(in_array($imageId + 1, $breakpoints), 'rapidez::product.partials.gallery.thumbnails.show-more')
        </button>
    @endfor
</div>
