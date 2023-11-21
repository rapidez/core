<add-to-cart :default-qty="{{ $product->min_sale_qty > $product->qty_increments ? $product->min_sale_qty : $product->qty_increments }}">
    <form slot-scope="{ _renderProxy: addToCartSlotProps, options, customOptions, tierPrices, currentTierPrice, error, add, disabledOptions, simpleProduct, adding, added, setCustomOptionFile }" v-on:submit.prevent="add">
        <h1 class="mb-3 text-3xl font-bold" itemprop="name">{{ $product->name }}</h1>
        @if (!$product->in_stock)
            <p class="text-red-600">@lang('Sorry! This product is currently out of stock.')</p>
        @else
            @include('rapidez::product.partials.addtocart.configurations')
            @include('rapidez::product.partials.addtocart.options')
            @include('rapidez::product.partials.addtocart.tierprices')

            <div class="mt-5 flex flex-wrap items-center gap-3">
                <x-rapidez::price
                    class="text-2xl font-bold text-neutral flex-col items-start gap-0"
                    options="{ product_options: customOptions, tier_price: currentTierPrice }"
                >
                    {{ price($product->special_price ?: $product->price) }}
                    <x-slot:special class="text-lg">
                        {{ $product->special_price ? price($product->price) : '' }}
                    </x-slot:special>
                </x-rapidez::price>

                <x-rapidez::select
                    class="w-auto"
                    name="qty"
                    label="Quantity"
                    v-model="addToCartSlotProps.qty"
                    labelClass="flex items-center sr-only"
                    wrapperClass="flex"
                >
                    @for ($i = $product->qty_increments; $i <= $product->qty_increments * min(10, floor(($product->qty ?? $product->qty_increments * 10)/ $product->qty_increments)); $i += $product->qty_increments)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </x-rapidez::select>

                <x-rapidez::button type="submit" class="flex items-center" dusk="add-to-cart">
                    <x-heroicon-o-shopping-cart class="mr-2 h-5 w-5" v-if="!adding && !added" />
                    <x-heroicon-o-arrow-path class="mr-2 h-5 w-5 animate-spin" v-if="adding" v-cloak />
                    <x-heroicon-o-check class="mr-2 h-5 w-5" v-if="added" v-cloak />
                    <span v-if="!adding && !added">@lang('Add to cart')</span>
                    <span v-if="adding" v-cloak>@lang('Adding')...</span>
                    <span v-if="added" v-cloak>@lang('Added')</span>
                </x-rapidez::button>
            </div>
        @endif
    </form>
</add-to-cart>
