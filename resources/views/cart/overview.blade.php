@extends('rapidez::layouts.app')

@section('title', 'Cart')

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <h1 class="mb-5 text-4xl font-bold">@lang('Cart')</h1>
        <cart v-cloak>
            <div
                v-if="hasItems"
                slot-scope="{ cart, hasItems, changeQty, remove }"
            >
                <div class="flex flex-col lg:flex-row gap-x-6">
                    <div class="flex w-full flex-col">
                        <div
                            class="mb-4 flex flex-wrap items-center border-b pb-2"
                            v-for="(item, itemId, index) in cart.items"
                        >
                            <div class="w-1/6 pr-3 sm:w-1/12">
                                <a
                                    class="block"
                                    :href="item.url | url"
                                >
                                    <img
                                        class="mx-auto"
                                        :alt="item.name"
                                        :src="'/storage/resizes/80x80/catalog/product' + item.image + '.webp'"
                                        height="100"
                                    />
                                </a>
                            </div>
                            <div class="w-5/6 sm:w-5/12 xl:w-6/12">
                                <a
                                    class="font-bold"
                                    :href="item.url | url"
                                    dusk="cart-item-name"
                                >@{{ item.name }}</a>
                                <div v-for="(optionValue, option) in item.options">
                                    @{{ option }}: @{{ optionValue }}
                                </div>
                                <div v-for="option in cart.items2.find((item) => item.item_id == itemId).options.filter((option) => !['info_buyRequest', 'option_ids'].includes(option.code))">
                                    @{{ option.label }}: @{{ option.value.title || option.value }}
                                </div>
                            </div>
                            <div class="w-2/6 pr-5 text-right sm:w-1/6 xl:w-2/12">
                                @{{ item.price | price }}
                            </div>
                            <div class="w-2/6 sm:w-1/6 xl:w-1/12">
                                <div class="inline-flex lg:w-3/4 xl:w-full">
                                    <x-rapidez::input
                                        class="text-right"
                                        name="qty"
                                        type="number"
                                        :label="false"
                                        v-model="item.qty"
                                        v-bind:dusk="'qty-'+index"
                                        ::min="item.min_sale_qty > item.qty_increments ? item.min_sale_qty : item.qty_increments"
                                        ::step="item.qty_increments"
                                    />
                                    <x-rapidez::button
                                        class="ml-1"
                                        :title="__('Update')"
                                        v-on:click="changeQty(item)"
                                        v-bind:dusk="'item-update-'+index"
                                    >
                                        <x-heroicon-s-refresh class="h-4 w-4" />
                                    </x-rapidez::button>
                                </div>
                            </div>
                            <div class="flex w-2/6 items-center justify-end text-right sm:w-1/6 xl:w-2/12">
                                @{{ item.total | price }}
                                <a
                                    class="ml-2"
                                    href="#"
                                    title="@lang('Remove')"
                                    @click.prevent="remove(item)"
                                    :dusk="'item-delete-' + index"
                                >
                                    <x-heroicon-s-x class="h-4 w-4" />
                                </a>
                            </div>
                        </div>
                        <div class="w-full md:w-auto">
                            @include('rapidez::cart.coupon')
                        </div>
                    </div>

                    <div class="flex flex-wrap items-start justify-between">
                        <div class="flex w-full md:w-auto">
                            <div class="flex w-full flex-wrap justify-end self-baseline lg:w-64">
                                <dl class="mb-5 flex w-full flex-wrap rounded-lg border p-3 [&>dd:nth-last-of-type(-n+2)]:border-none [&>dd]:w-1/2 [&>dd]:border-b [&>dd]:py-3">
                                    <dd>@lang('Subtotal')</dd>
                                    <dd class="text-right">@{{ cart.subtotal | price }}</dd>
                                    <dd v-if="cart.tax > 0">@lang('Tax')</dd>
                                    <dd
                                        class="text-right"
                                        v-if="cart.tax > 0"
                                    >@{{ cart.tax | price }}</dd>
                                    <dd v-if="cart.shipping_amount > 0">@lang('Shipping')<br><small>@{{ cart.shipping_description }}</small></dd>
                                    <dd
                                        class="text-right"
                                        v-if="cart.shipping_amount > 0"
                                    >@{{ cart.shipping_amount | price }}</dd>
                                    <dd v-if="cart.discount_name && cart.discount_amount < 0">@lang('Discount'): @{{ cart.discount_name }}</dd>
                                    <dd v-if="!cart.discount_name && cart.discount_amount < 0">@lang('Discount')</dd>
                                    <dd
                                        class="text-right"
                                        v-if="cart.discount_amount < 0"
                                    >@{{ cart.discount_amount | price }}</dd>
                                    <dd class="font-bold">@lang('Total')</dd>
                                    <dd class="text-right font-bold">@{{ cart.total | price }}</dd>
                                </dl>

                                <x-rapidez::button
                                    href="{{ route('checkout') }}"
                                    dusk="checkout"
                                >
                                    @lang('Checkout')
                                </x-rapidez::button>
                            </div>
                        </div>
                    </div>
                </div>

                <x-rapidez::productlist
                    value="cart.cross_sells"
                    title="More choices to go with your product"
                    field="id"
                />
            </div>
            <div v-else>
                @lang('You don\'t have anything in your cart.')
            </div>
        </cart>
    </div>
@endsection
