<images>
    <div slot-scope="{ images }">
        <div v-if="!images" class="{{ in_array(count($product->images), [0, 1]) ? '' : 'grid gap-3 grid-cols-2' }}">
            @forelse($product->images as $image)
                <div class="flex items-center justify-center border rounded p-5 {{ count($product->images) == 1 ? 'h-96' : 'h-48 sm:h-64 md:h-80 lg:h-96' }}">
                    <img
                        src="/storage/resizes/450/catalog/product{{ $image }}"
                        alt="{{ $product->name }}"
                        class="max-h-full max-w-full"
                        loading="lazy"
                    />
                </div>
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
            <div
                v-for="image in images"
                class="flex items-center justify-center border rounded p-5"
                :class="images.length == 1 ? 'h-96' : 'h-48 sm:h-64 md:h-80 lg:h-96'"
            >
                <img
                    :src="'/storage/resizes/450/catalog/product' + image"
                    alt="{{ $product->name }}"
                    class="max-h-full max-w-full"
                    loading="lazy"
                />
            </div>
            <x-rapidez::no-image v-if="images.length == 0" class="rounded h-96"/>
        </div>
    </div>
</images>
