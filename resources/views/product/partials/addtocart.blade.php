<add-to-cart :default-qty="{{ $product->min_sale_qty > $product->qty_increments ? $product->min_sale_qty : $product->qty_increments }}" v-slot="addToCart">
    <form v-on:submit.prevent="add" class="flex flex-col gap-5">
        <h1 class="text-3xl font-bold" itemprop="name">{{ $product->name }}</h1>
        @if (!$product->in_stock)
            <p class="text-red-600">@lang('Sorry! This product is currently out of stock.')</p>
        @else
            @include('rapidez::product.partials.super_attributes')
            @include('rapidez::product.partials.options')

            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <div class="text-2xl font-bold text-neutral" v-text="$options.filters.price(addToCart.specialPrice || addToCart.price)">
                        {{ price($product->special_price ?: $product->price) }}
                    </div>
                    <div class="text-lg text-neutral line-through" v-if="addToCart.specialPrice" v-text="$options.filters.price(addToCart.price)">
                        {{ $product->special_price ? price($product->price) : '' }}
                    </div>
                </div>
                <x-rapidez::select
                    class="w-auto"
                    name="qty"
                    label="Quantity"
                    v-model="addToCart.qty"
                    labelClass="flex items-center sr-only"
                    wrapperClass="flex"
                >
                    @for ($i = $product->qty_increments; $i <= $product->qty_increments * 10; $i += $product->qty_increments)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </x-rapidez::select>
                <x-rapidez::button type="submit" class="flex items-center" dusk="add-to-cart">
                    <x-heroicon-o-shopping-cart class="mr-2 h-5 w-5" v-if="!addToCart.adding && !addToCart.added" />
                    <x-heroicon-o-arrow-path class="mr-2 h-5 w-5 animate-spin" v-if="addToCart.adding" v-cloak />
                    <x-heroicon-o-check class="mr-2 h-5 w-5" v-if="addToCart.added" v-cloak />
                    <span v-if="!addToCart.adding && !addToCart.added">@lang('Add to cart')</span>
                    <span v-if="addToCart.adding" v-cloak>@lang('Adding')...</span>
                    <span v-if="addToCart.added" v-cloak>@lang('Added')</span>
                </x-rapidez::button>
            </div>
        @endif
    </form>
</add-to-cart>
