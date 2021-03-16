<div class="{{ $product->images->count() == 1 ? '' : 'grid gap-3 grid-cols-2' }}">
    @forelse($product->images as $image)
        <div class="flex items-center justify-center border rounded p-5 {{ $product->images->count() == 1 ? 'h-96' : 'h-48 sm:h-64 md:h-80 lg:h-96' }}">
            <img
                src="/storage/resizes/450/catalog/product{{ $image->value }}"
                alt="{{ $product->name }}"
                class="max-h-full max-w-full"
                loading="lazy"
            />
        </div>
    @empty
        <x-rapidez::no-image class="rounded h-64"/>
    @endforelse
</div>
