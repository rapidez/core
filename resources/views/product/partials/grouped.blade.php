<div class="flex flex-wrap gap-3 justify-between">
    @foreach ($product->grouped as $groupedProduct)
        <add-to-cart :product="config.product.grouped[{{ $groupedProduct->entity_id }}]" v-slot="addToCart" class="contents">
            <form v-on:submit.prevent="addToCart.add" class="contents">
                <div>
                    {{ $groupedProduct->name }}
                    <div class="flex items-center space-x-3 font-bold">
                        <div>{{ price($groupedProduct->special_price ?: $groupedProduct->price) }}</div>
                        @if ($groupedProduct->special_price)
                            <div class="line-through">{{ price($groupedProduct->price) }}</div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if (!$groupedProduct->in_stock)
                        <p class="col-span-2 self-center text-red-600">
                            @lang('Sorry! This product is currently out of stock.')
                        </p>
                    @else
                        <x-rapidez::quantity
                            v-model.number="addToCart.qty"
                            ::min="{{ $groupedProduct->min_sale_qty }}"
                            ::step="{{ $groupedProduct->qty_increments }}"
                            ::max="{{ $groupedProduct->max_sale_qty ?: 'null' }}"
                        />

                        <x-rapidez::button.cart />
                    @endif
                </div>
            </form>
        </add-to-cart>
    @endforeach
</div>
