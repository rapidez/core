<add-to-cart :default-qty="{{ $product->min_sale_qty > $product->qty_increments ? $product->min_sale_qty : $product->qty_increments }}" v-slot="addToCart">
    <form v-on:submit.prevent="addToCart.add" class="flex flex-col gap-5">
        <h1 class="text-3xl font-bold" itemprop="name">{{ $product->name }}</h1>
        @if (!$product->in_stock && $product->backorder_type === 0)
            <p class="text-red-600">@lang('Sorry! This product is currently out of stock.')</p>
        @else
            @include('rapidez::product.partials.super_attributes')
            @include('rapidez::product.partials.options')

            @if ($product->qty <= 0 && $product->backorder_type == 2)
                <div class="flex gap-2">
                    <x-heroicon-o-exclamation-circle class="mt-px w-5" />
                    <span>@lang('This product will be backordered')</span>
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <div class="text-2xl font-bold text" v-text="$options.filters.price(addToCart.specialPrice || addToCart.price)">
                        {{ price($product->special_price ?: $product->price) }}
                    </div>
                    <div class="text-lg text-muted line-through" v-if="addToCart.specialPrice" v-text="$options.filters.price(addToCart.price)">
                        {{ $product->special_price ? price($product->price) : '' }}
                    </div>
                </div>

                @include('rapidez::product.partials.quantity')

                <x-rapidez::button.cart/>
            </div>
        @endif
    </form>
</add-to-cart>
