{{--
    In this file we avoid layout shifts on the page by pre-populating the possible amount of thumbnail slots.
    On page load all of the images come from PHP, using `@unless (...) v-cloak @endunless` to hide things that
    shouldn't be shown, but need a placeholder element.
    After Vue gets loaded in, it will take over and replace the `v-cloak`s with the `v-if`s, which means that
    the two conditionals should be identical to avoid layout shifts.

    The `$breakpoints` variable determines how many images are shown on each individual breakpoint.
--}}

@php($breakpoints = ['xl' => 7, 'lg' => 5, 'md' => 7, 'sm' => 5, 'xs' => 3])

<div class="mt-3 flex gap-2">
    @for ($imageId = 0; $imageId < max($breakpoints); $imageId++)
        <button
            @unless ($imageId < count($product->images)) v-cloak @endunless
            v-if="{{ $imageId }} < images.length"
            @class([
                'max-w-24 relative flex aspect-square flex-1 items-center justify-center overflow-hidden rounded border bg-white p-1.5 outline-primary transition-all',
                'outline outline-1 border-primary' => $imageId == 0,
                'xl:hidden' => $imageId >= $breakpoints['xl'],
                'lg:max-xl:hidden' => $imageId >= $breakpoints['lg'],
                'md:max-lg:hidden' => $imageId >= $breakpoints['md'],
                'sm:max-md:hidden' => $imageId >= $breakpoints['sm'],
                'max-sm:hidden' => $imageId >= $breakpoints['xs'],
            ])
            v-bind:class="{
                'outline outline-1 border-primary': active === {{ $imageId }},
                '!outline-transparent !outline-0 !border-border': active !== {{ $imageId }},
            }"
            v-on:click="change({{ $imageId }})"
        >
            <img
                {{-- Note: always put the `src` before a `v-bind:src` or it will not work --}}
                @if($imageId < count($product->images))
                    src="/storage/{{ config('rapidez.store') }}/resizes/80x80/magento/catalog/product/{{ $product->images[$imageId] }}.webp"
                @endif
                v-bind:src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/magento/catalog/product/' + images[{{ $imageId }}] + '.webp'"
                alt="{{ $product->name }}"
                class="block max-h-full w-auto object-contain"
                width="80"
                height="80"
            />

            @if($imageId + 1 >= min($breakpoints))
                <div
                    @unless (count($product->images) - $imageId - 1) v-cloak @endunless
                    v-if="images.length - {{ $imageId }} - 1"
                >
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
            @endif
        </button>
    @endfor
</div>
