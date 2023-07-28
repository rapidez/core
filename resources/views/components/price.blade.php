@props(['product' => 'product', 'type' => 'catalog', 'placeholder' => ''])

<price :product="{{ $product }}" type="{{ $type }}">
    <div
        {{ $attributes->class('mt-1 flex items-center gap-2 text-14 font-semibold text-primary') }}
        slot-scope="{ price, specialPrice, isDiscounted, range }"
    >
        <template v-if="range">
            <span>
                <template v-if="range.min != range.max">
                    @{{ range.min | price }} - @{{ range.max | price }}
                </template>
                <template v-else>
                    @{{ range.min | price }}
                </template>
            </span>
        </template>
        <template v-else>
            <span v-text="$options.filters.price(specialPrice || price)">
                {{ $placeholder }}
            </span>
            <span
                class="text-13 font-normal line-through"
                v-if="isDiscounted"
            >
                @{{ price | price }}
            </span>
        </template>
    </div>
</price>
