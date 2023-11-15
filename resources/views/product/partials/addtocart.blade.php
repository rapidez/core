<add-to-cart :default-qty="{{ $product->min_sale_qty > $product->qty_increments ? $product->min_sale_qty : $product->qty_increments }}">
    <form slot-scope="{ _renderProxy: addToCartSlotProps, options, customOptions, error, add, disabledOptions, simpleProduct, adding, added, price, specialPrice, setCustomOptionFile }" v-on:submit.prevent="add" class="flex flex-col gap-5">
        <h1 class="text-3xl font-bold" itemprop="name">{{ $product->name }}</h1>
        @if (!$product->in_stock)
            <p class="text-red-600">@lang('Sorry! This product is currently out of stock.')</p>
        @else
            @include('rapidez::product.partials.super_attributes')
            @include('rapidez::product.partials.options')

            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <div class="text-2xl font-bold text-neutral" v-text="$options.filters.price(specialPrice || price)">
                        {{ price($product->special_price ?: $product->price) }}
                    </div>
                    <div class="text-lg text-neutral line-through" v-if="specialPrice" v-text="$options.filters.price(price)">
                        {{ $product->special_price ? price($product->price) : '' }}
                    </div>
                </div>
                <x-rapidez::select
                    class="w-auto"
                    name="qty"
                    label="Quantity"
                    v-model="addToCartSlotProps.qty"
                    labelClass="flex items-center sr-only"
                    wrapperClass="flex"
                >
                    @for ($i = $product->qty_increments; $i <= $product->qty_increments * 10; $i += $product->qty_increments)
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
