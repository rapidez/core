<div class="flex flex-wrap gap-3 justify-between">
    @foreach ($product->children as $groupedProduct)
        <add-to-cart :product="config.product.children[{{ $groupedProduct->entity_id }}]" v-slot="addToCart">
            <form v-on:submit.prevent="addToCart.add" class="flex w-full justify-between max-lg:flex-wrap max-lg:gap-y-2">
                <div class="lg:max-xl:max-w-36">
                    {{ $groupedProduct->name }}
                    <div class="flex items-center space-x-3 font-bold mr-auto">
                        <div>{{ price($groupedProduct->special_price ?: $groupedProduct->price) }}</div>
                        @if ($groupedProduct->special_price)
                            <div class="line-through">{{ price($groupedProduct->price) }}</div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 lg:ml-auto">
                    @if (!$groupedProduct->stock->is_in_stock)
                        <p class="col-span-2 self-center text-red-600">
                            @lang('Sorry! This product is currently out of stock.')
                        </p>
                    @else
                        <x-rapidez::quantity
                            v-model.number="addToCart.qty"
                            ::min="{{ $groupedProduct->stock->min_sale_qty }}"
                            ::step="{{ $groupedProduct->stock->qty_increments }}"
                            ::max="{{ $groupedProduct->stock->max_sale_qty ?: 'null' }}"
                        />

                        <x-rapidez::button.cart class="self-start" />
                    @endif
                </div>
            </form>
        </add-to-cart>
    @endforeach
</div>
