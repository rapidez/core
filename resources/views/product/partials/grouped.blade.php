<div class="grid grid-cols-3 gap-3 grid-cols-[auto_max-content_max-content]">
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

                @if (!$groupedProduct->in_stock)
                    <p class="col-span-2 self-center text-red-600">
                        @lang('Sorry! This product is currently out of stock.')
                    </p>
                @else
                    @include('rapidez::product.partials.quantity', ['product' => $groupedProduct])

                    <x-rapidez::button.cart/>
                @endif
            </form>
        </add-to-cart>
    @endforeach
</div>
