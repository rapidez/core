@props(['product' => 'product', 'type' => 'catalog'])
@slots(['special'])

<price :product="{{ $product }}" type="{{ $type }}">
    <div
        {{ $attributes->twMerge('mt-1 flex items-center gap-2') }}
        slot-scope="{ price, specialPrice, isDiscounted, range }"
    >
        <span v-text="$options.filters.price(specialPrice || price)" v-if="!range">
            {{ $slot }}
        </span>
        <span
            {{ $special->attributes->twMerge('text-13 font-normal line-through') }}
            v-if="isDiscounted && !range"
            v-text="$options.filters.price(price)"
        >
            {{ $special }}
        </span>

        <span v-if="range">
            <template v-if="range.min != range.max">
                @{{ range.min | price }} - @{{ range.max | price }}
            </template>
            <template v-else>
                @{{ range.min | price }}
            </template>
        </span>
    </div>
</price>
