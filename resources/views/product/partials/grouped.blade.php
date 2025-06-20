<div class="flex flex-wrap gap-3 justify-between">
    @foreach ($product->grouped as $groupedProduct)
        <add-to-cart :product="config.product.grouped[{{ $groupedProduct->entity_id }}]" v-slot="addToCart" class="w-full">
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
                    @if (!$groupedProduct->in_stock)
                        <p class="col-span-2 self-center text-red-600">
                            @lang('Sorry! This product is currently out of stock.')
                        </p>
                    @else
                        <x-rapidez::quantity
                            v-model.number="addToCart.qty"
                            ::min="{{ max($groupedProduct->min_sale_qty, $groupedProduct->qty_increments) }}"
                            ::step="{{ $groupedProduct->qty_increments }}"
                            ::max="{{ $groupedProduct->max_sale_qty ?: 'null' }}"
                        />

                        <x-rapidez::button.cart class="self-start" />
                    @endif
                </div>
            </form>
        </add-to-cart>
    @endforeach
</div>
