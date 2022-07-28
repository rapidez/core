@extends('rapidez::layouts.app')

@section('title', 'Cart')

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <h1 class="font-bold text-4xl mb-5">@lang('Cart')</h1>

    <cart v-cloak>
        <div v-if="hasItems" slot-scope="{ cart, hasItems, changeQty, remove }">
            <div class="flex flex-wrap items-center border-b pb-2 mb-2" v-for="(item, productId, index) in cart.items">
                <div class="w-1/6 sm:w-1/12 pr-3">
                    <a :href="item.url" class="block">
                        <picture>
                            <source :srcset="'/storage/resizes/80x80/catalog/product' + item.image + '.webp'" type="image/webp">
                            <img
                                :alt="item.name"
                                :src="'/storage/resizes/80x80/catalog/product' + item.image"
                                height="100"
                                class="mx-auto"
                            />
                        </picture>
                    </a>
                </div>
                <div class="w-5/6 sm:w-5/12 lg:w-8/12">
                    <a :href="item.url" dusk="cart-item-name" class="font-bold">@{{ item.name }}</a>
                    <div v-for="(optionValue, option) in item.options">
                        @{{ option }}: @{{ optionValue }}
                    </div>
                </div>
                <div class="w-2/6 sm:w-1/6 lg:w-1/12 text-right pr-5">
                    @{{ item.price | price }}
                </div>
                <div class="w-2/6 sm:w-1/6 lg:w-1/12">
                    <div class="inline-flex">
                        <x-rapidez::input
                            :label="false"
                            class="text-right"
                            type="number"
                            name="qty"
                            v-model="item.qty"
                            v-bind:dusk="'qty-'+index"
                            ::min="item.min_sale_qty"
                            ::step="item.qty_increments"
                        />
                        <x-rapidez::button
                            class="ml-1"
                            :title="__('Update')"
                            v-on:click="changeQty(item)"
                            v-bind:dusk="'item-update-'+index"
                        >
                            <x-heroicon-s-refresh class="w-4 h-4"/>
                        </x-rapidez::button>
                    </div>
                </div>
                <div class="w-2/6 sm:w-1/6 lg:w-1/12 flex justify-end items-center text-right">
                    @{{ item.total | price }}
                    <a href="#" @click.prevent="remove(item)" class="ml-2" title="@lang('Remove')" :dusk="'item-delete-'+index">
                        <x-heroicon-s-x class="w-4 h-4"/>
                    </a>
                </div>
            </div>

            <div class="sm:flex sm:justify-between sm:items-start mt-5">
                @include('rapidez::cart.coupon')
                <div class="flex flex-wrap justify-end sm:w-64">
                    <div class="flex flex-wrap w-full p-3 mb-5 bg-secondary rounded-lg">
                        <div class="w-1/2">@lang('Subtotal')</div>
                        <div class="w-1/2 text-right">@{{ cart.subtotal | price }}</div>
                        <div class="w-1/2" v-if="cart.tax > 0">@lang('Tax')</div>
                        <div class="w-1/2 text-right" v-if="cart.tax > 0">@{{ cart.tax | price }}</div>
                        <div class="w-1/2" v-if="cart.shipping_amount > 0">@lang('Shipping')<br><small>@{{ cart.shipping_description }}</small></div>
                        <div class="w-1/2 text-right" v-if="cart.shipping_amount > 0">@{{ cart.shipping_amount | price }}</div>
                        <div class="w-1/2" v-if="cart.discount_name && cart.discount_amount < 0">@lang('Discount'): @{{ cart.discount_name }}</div>
                        <div class="w-1/2" v-if="!cart.discount_name && cart.discount_amount < 0">@lang('Discount')</div>
                        <div class="w-1/2 text-right" v-if="cart.discount_amount < 0">@{{ cart.discount_amount | price }}</div>
                        <div class="w-1/2 font-bold">@lang('Total')</div>
                        <div class="w-1/2 text-right font-bold">@{{ cart.total | price }}</div>
                    </div>

                    <x-rapidez::button href="/checkout" dusk="checkout">
                        @lang('Checkout')
                    </x-rapidez::button>
                </div>
            </div>

            <x-rapidez::productlist title="More choices to go with your product" field="id" value="cart.cross_sells"/>
        </div>
        <div v-else>
            @lang('You don\'t have anything in your cart.')
        </div>
    </cart>
@endsection
