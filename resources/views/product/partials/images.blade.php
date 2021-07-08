<images>
    <div slot-scope="{ images, opened, toggle }">
        <div v-if="!images" class="{{ in_array(count($product->images), [0, 1]) ? '' : 'grid gap-3 grid-cols-2' }}">
            @forelse($product->images as $image)
                <a
                    href="{{ config('rapidez.media_url').'/catalog/product'.$image }}"
                    class="flex items-center justify-center border rounded p-5 {{ count($product->images) == 1 ? 'h-96' : 'h-48 sm:h-64 md:h-80 lg:h-96' }}"
                    :class="{ 'fixed inset-0 bg-white !h-full z-10 cursor-[zoom-out]': opened === {{ $loop->index }} }"
                    v-on:click.prevent="toggle({{ $loop->index }}, $event)"
                >
                    <picture class="contents">
                        <source srcset="/storage/resizes/450/catalog/product{{ $image }}.webp" type="image/webp">
                        <img
                            src="/storage/resizes/450/catalog/product{{ $image }}"
                            alt="{{ $product->name }}"
                            class="max-h-full max-w-full"
                            loading="lazy"
                        />
                    </picture>
                </a>
            @empty
                <x-rapidez::no-image class="rounded h-96"/>
            @endforelse
        </div>

        {{--
        We've some duplication here and that's because on the first load we want to display the
        images as fast as possible. But when we change product options they've to change.
        So the first load we use Blade and when options change we use Vue.
        --}}
        <div v-cloak v-else :class="[0, 1].includes(images.length) ? '' : 'grid gap-3 grid-cols-2'">
            <a
                :href="config.media_url + '/catalog/product' + image"
                v-for="(image, index) in images"
                class="flex items-center justify-center border rounded p-5"
                :class="{
                    'h-96': images.length == 1,
                    'h-48 sm:h-64 md:h-80 lg:h-96': images.length != 1,
                    'fixed inset-0 bg-white !h-full z-10 cursor-[zoom-out]': opened === index
                }"
                v-on:click.prevent="toggle(index, $event)"
            >
                <picture class="contents">
                    <source :srcset="'/storage/resizes/450/catalog/product' + image + '.webp'" type="image/webp">
                    <img
                        :src="'/storage/resizes/450/catalog/product' + image"
                        alt="{{ $product->name }}"
                        class="max-h-full max-w-full"
                        loading="lazy"
                    />
                </picture>
            </a>
            <x-rapidez::no-image v-if="images.length == 0" class="rounded h-96"/>
        </div>

        <div v-if="opened !== false" class="fixed top-3 right-3 z-10 pointer-events-none">
            <x-heroicon-o-x class="h-6 w-6"/>
        </div>
    </div>
</images>
