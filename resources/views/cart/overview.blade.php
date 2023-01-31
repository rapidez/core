@extends('rapidez::layouts.app')

@section('title', 'Cart')

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <h1 class="font-bold text-4xl mb-5">@lang('Cart')</h1>
        <cart v-cloak>
            <div v-if="hasItems" slot-scope="{ cart, hasItems, changeQty, remove }">
                <div class="flex flex-col lg:flex-row lg:gap-x-6">
                    <div class="flex flex-col w-full">
                        <div class="flex flex-wrap items-center mb-4 border-b pb-2"  v-for="(item, productId, index) in cart.items">
                            <div class="w-1/6 sm:w-1/12 pr-3">
                                <a :href="item.url" class="block">
                                    <img
                                        :alt="item.name"
                                        :src="'/storage/resizes/80x80/catalog/product' + item.image + '.webp'"
                                        height="100"
                                        class="mx-auto"
                                    />
                                </a>
                            </div>
                            <div class="w-5/6 sm:w-5/12 lg:w-5/12">
                                <a :href="item.url" dusk="cart-item-name" class="font-bold">@{{ item.name }}</a>
                                <div v-for="(optionValue, option) in item.options">
                                    @{{ option }}: @{{ optionValue }}
                                </div>
                            </div>
                            <div class="w-2/6 sm:w-1/6 lg:w-2/12 text-right pr-5">
                                @{{ item.price | price }}
                            </div>
                            <div class="w-2/6 sm:w-1/6 lg:w-2/12">
                                <div class="inline-flex xl:w-3/4">
                                    <x-rapidez::input
                                        :label="false"
                                        class="text-right"
                                        type="number"
                                        name="qty"
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
                                        <x-heroicon-s-refresh class="w-4 h-4"/>
                                    </x-rapidez::button>
                                </div>
                            </div>
                            <div class="w-2/6 sm:w-1/6 lg:w-2/12 flex justify-end items-center text-right">
                                @{{ item.total | price }}
                                <a href="#" @click.prevent="remove(item)" class="ml-2" title="@lang('Remove')" :dusk="'item-delete-'+index">
                                    <x-heroicon-s-x class="w-4 h-4"/>
                                </a>
                            </div>
                        </div>
                        @include('rapidez::cart.coupon')
                    </div>

                    <div class="flex mt-5">
                        <div class="flex flex-wrap justify-end w-full self-baseline lg:w-64">
                            <div class="flex flex-wrap w-full p-3 mb-5 border rounded-lg">
                                <div class="w-1/2 py-3 border-b">@lang('Subtotal')</div>
                                <div class="w-1/2 text-right py-3 border-b">@{{ cart.subtotal | price }}</div>
                                <div class="w-1/2 py-3 border-b" v-if="cart.tax > 0">@lang('Tax')</div>
                                <div class="w-1/2 text-right py-3 border-b" v-if="cart.tax > 0">@{{ cart.tax | price }}</div>
                                <div class="w-1/2 py-3 border-b" v-if="cart.shipping_amount > 0">@lang('Shipping')<br><small>@{{ cart.shipping_description }}</small></div>
                                <div class="w-1/2 text-right py-3 border-b" v-if="cart.shipping_amount > 0">@{{ cart.shipping_amount | price }}</div>
                                <div class="w-1/2 py-3 border-b" v-if="cart.discount_name && cart.discount_amount < 0">@lang('Discount'): @{{ cart.discount_name }}</div>
                                <div class="w-1/2 py-3 border-b" v-if="!cart.discount_name && cart.discount_amount < 0">@lang('Discount')</div>
                                <div class="w-1/2 text-right py-3 border-b" v-if="cart.discount_amount < 0">@{{ cart.discount_amount | price }}</div>
                                <div class="w-1/2 font-bold py-3">@lang('Total')</div>
                                <div class="w-1/2 text-right font-bold py-3">@{{ cart.total | price }}</div>
                            </div>

                            <x-rapidez::button href="/checkout" dusk="checkout">
                                @lang('Checkout')
                            </x-rapidez::button>
                        </div>
                    </div>
                </div>

                <x-rapidez::productlist title="More choices to go with your product" field="id" value="cart.cross_sells"/>
            </div>
            <div v-else>
                @lang('You don\'t have anything in your cart.')
            </div>
        </cart>
    </div>
@endsection
