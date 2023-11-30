{{-- TODO: Refactor to a table just like the checkout theme as things don't always line up as it should --}}
<div v-for="(item, index) in cart.items" class="relative flex gap-5 border-b py-3 max-lg:flex-col lg:items-center">
    <div class="flex flex-1 items-center gap-5">
        <a class="w-20" :href="item.product.url_key + item.product.url_suffix | url">
            <img class="mx-auto" :alt="item.product.name" :src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/magento' + item.product.image.url.replace(config.media_url, '') + '.webp'" height="80" />
        </a>
        <div class="flex flex-col items-start gap-1">
            <a class="font-bold" :href="item.product.url_key + item.product.url_suffix | url" dusk="cart-item-name">
                @{{ item.product.name }}
            </a>
            <div v-for="option in item.configurable_options">
                @{{ option.option_label }}: @{{ option.value_label }}
            </div>
            <div v-for="option in item.customizable_options">
                @{{ option.label }}: @{{ option.values[0].label || option.values[0].value }}
            </div>
            @include('rapidez::cart.item.remove')
        </div>
    </div>
    <div class="flex items-center justify-between gap-5">
        @{{ item.prices.price_including_tax.value | price }}
        @include('rapidez::cart.item.quantity')
        @{{ item.prices.row_total_including_tax.value | price }}
    </div>
</div>
