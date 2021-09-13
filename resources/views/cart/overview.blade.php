@extends('rapidez::layouts.app')

@section('title', 'Cart')

@section('content')
    <h1 class="font-bold text-4xl mb-5">@lang('Cart')</h1>
    <cart v-if="mask" v-cloak>
        <div v-if="cart && cart.items && Object.keys(cart.items).length" slot-scope="{ data, refreshCart, cartCrossells }">
            <div class="flex flex-wrap items-center border-b pb-2 mb-2" v-for="(item, index) in cart.items">
                <div class="w-1/6 sm:w-1/12 pr-3">
                    <a :href="item.product.url_key + item.product.url_suffix" class="block">
                        <picture>
                            <source :srcset="'/storage/resizes/100x100/catalog/product' + item.product.image.url.replace(config.media_url + '/catalog/product', '') + '.webp'" type="image/webp">
                            <img
                                :alt="item.product.name"
                                :src="'/storage/resizes/100x100/catalog/product' + item.product.image.url.replace(config.media_url + '/catalog/product', '')"
                                height="100"
                                class="mx-auto"
                            />
                        </picture>
                    </a>
                </div>
                <div class="w-5/6 sm:w-5/12 lg:w-8/12">
                    <a :href="item.product.url_key + item.product.url_suffix" dusk="cart-item-name" class="font-bold">@{{ item.product.name }}</a>
                    <div v-for="(option) in item.configurable_options">
                        @{{ option.option_label }}: @{{ option.value_label }}
                    </div>
                </div>
                <div class="w-2/6 sm:w-1/6 lg:w-1/12 text-right pr-5">
                    @{{ item.prices.price.value | price }}
                </div>
                <graphql-mutation :callback="refreshCart" :changes="{cart_id: cart.id, cart_items: {cart_item_id: Number(item.id), quantity: Number(item.quantity)}}" query='@include("rapidez::cart.queries.changeQty")'>
                    <div class="w-2/6 sm:w-1/6 lg:w-1/12" slot-scope="{ changes, mutate, mutated }">
                        <div class="inline-flex">
                            <x-rapidez::input
                                :label="false"
                                class="text-right"
                                type="number"
                                min="1"
                                name="qty"
                                v-model="item.quantity"
                                v-bind:dusk="'qty-'+index"
                            />
                            <x-rapidez::button
                                class="ml-1"
                                :title="__('Update')"
                                v-on:click="mutate()"
                                v-bind:dusk="'item-update-'+index"
                            >
                                <x-heroicon-s-refresh class="w-4 h-4"/>
                            </x-rapidez::button>
                        </div>
                    </div>
                </graphql-mutation>
                <div class="w-2/6 sm:w-1/6 lg:w-1/12 flex justify-end items-center text-right">
                    @{{ item.prices.price.value * item.quantity | price }}
                    <graphql-mutation :callback="refreshCart" :changes="{ cart_id: cart.id, cart_item_id: Number(item.id) }" query="@include('rapidez::cart.queries.removeItemFromCart')">
                        <a :disabled="$root.loading" slot-scope="{changes, mutate}" href="#" v-on:click.prevent="mutate()" class="ml-2" title="@lang('Remove')" :dusk="'item-delete-'+index">
                            <x-heroicon-s-x class="w-4 h-4"/>
                        </a>
                    </graphql-mutation>
                </div>
            </div>

            <div class="sm:flex sm:justify-between sm:items-start mt-5">
                @include('rapidez::cart.coupon')
                <div class="flex flex-wrap justify-end sm:w-64">
                    <div class="flex flex-wrap w-full p-3 mb-5 bg-secondary rounded-lg">
                        <div class="w-1/2">@lang('Subtotal')</div>
                        <div class="w-1/2 text-right">@{{ cart.prices.subtotal_excluding_tax.value | price }}</div>
                        <div v-if="cart.prices.applied_taxes.length" class="w-1/2">@lang('Tax')</div>
                        <div v-if="cart.prices.applied_taxes.length" v-for="(tax) in cart.prices.applied_taxes" class="w-1/2 text-right">@{{ tax | price }}</div>
                        <div class="w-1/2" v-if="cart.prices.discounts.length" v-for="(discount) in cart.prices.discounts">@{{ discount.label }}:</div>
                        <div class="w-1/2 text-right" v-if="cart.prices.discounts.length" v-for="(discount) in cart.prices.discounts">@{{ discount.amount.value | price }}</div>
                        <div class="w-1/2 font-bold">@lang('Total'):</div>
                        <div class="w-1/2 text-right font-bold">@{{ cart.prices.grand_total.value | price }}</div>
                    </div>

                    <x-rapidez::button href="/checkout" dusk="checkout">
                        @lang('Checkout')
                    </x-rapidez::button>
                </div>
            </div>

            <x-rapidez::productlist title="More choices to go with your product" field="id" value="cartCrossells"/>
        </div>
        <div v-else>
            @lang('You don\'t have anything in your cart.')
        </div>
    </cart>
    <div v-else>
        @lang('You don\'t have anything in your cart.')
    </div>
@endsection
