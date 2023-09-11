@extends('rapidez::layouts.app')

@section('title', 'Cart')

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <h1 class="mb-5 text-4xl font-bold">@lang('Cart')</h1>
        <cart v-cloak>
            <div v-if="hasItems" slot-scope="{ cart, hasItems, changeQty, remove }">
                <div class="flex flex-col gap-x-10 lg:flex-row">
                    <div class="flex w-full flex-col">
                        <div v-for="(item, itemId, index) in cart.items" class="relative flex gap-5 border-b py-3 max-lg:flex-col lg:items-center">
                            <div class="flex flex-1 items-center gap-5">
                                <a class="w-20":href="item.url | url">
                                    <img class="mx-auto" :alt="item.name" :src="'/storage/{{ config('rapidez.store') }}/resizes/80x80/magento/catalog/product' + item.image + '.webp'" height="100" />
                                </a>
                                <div class="flex flex-col items-start gap-1">
                                    <a class="font-bold" :href="item.url | url" dusk="cart-item-name">@{{ item.name }}</a>
                                    <div v-for="(optionValue, option) in item.options">
                                        @{{ option }}: @{{ optionValue }}
                                    </div>
                                    <div v-for="option in cart.items2.find((item) => item.item_id == itemId).options.filter((option) => !['info_buyRequest', 'option_ids'].includes(option.code) && option.label)">
                                        @{{ option.label }}: @{{ option.value.title || option.value }}
                                    </div>
                                    <button v-on:click="remove(item)" v-bind:dusk="'item-delete-' + index" class="cursor-pointer hover:underline" title="@lang('Remove')">
                                        @lang('Remove')
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-5">
                                @{{ item.price | price }}
                                <div class="flex items-center gap-1">
                                    <x-rapidez::input v-model="item.qty" v-bind:dusk="'qty-'+index" name="qty" class="w-14 px-1 text-center" type="number" :label="false" ::min="item.min_sale_qty > item.qty_increments ? item.min_sale_qty : item.qty_increments" ::step="item.qty_increments" />
                                    <x-rapidez::button v-on:click="changeQty(item)" v-bind:dusk="'item-update-'+index" class="ml-1" :title="__('Update')">
                                        <x-heroicon-s-arrow-path class="h-4 w-4" />
                                    </x-rapidez::button>
                                </div>
                                @{{ item.total | price }}
                            </div>
                        </div>
                        <div class="mt-5 self-start">
                            @include('rapidez::cart.coupon')
                        </div>
                    </div>

                    <div class="flex w-full flex-wrap justify-end self-baseline lg:max-w-xs">
                        <dl class="mb-5 flex w-full flex-wrap rounded-lg border p-3 [&>dd:nth-last-of-type(-n+2)]:border-none [&>dd]:w-1/2 [&>dd]:border-b [&>dd]:py-3">
                            <dd>@lang('Subtotal')</dd>
                            <dd class="text-right">@{{ cart.subtotal | price }}</dd>
                            <dd v-if="cart.tax > 0">@lang('Tax')</dd>
                            <dd v-if="cart.tax > 0" class="text-right">@{{ cart.tax | price }}</dd>
                            <dd v-if="cart.shipping_amount > 0">@lang('Shipping')<br><small>@{{ cart.shipping_description }}</small></dd>
                            <dd v-if="cart.shipping_amount > 0" class="text-right">@{{ cart.shipping_amount | price }}</dd>
                            <dd v-if="cart.discount_name && cart.discount_amount < 0">@lang('Discount'): @{{ cart.discount_name }}</dd>
                            <dd v-if="!cart.discount_name && cart.discount_amount < 0">@lang('Discount')</dd>
                            <dd v-if="cart.discount_amount < 0" class="text-right">@{{ cart.discount_amount | price }}</dd>
                            <dd class="font-bold">@lang('Total')</dd>
                            <dd class="text-right font-bold">@{{ cart.total | price }}</dd>
                        </dl>
                        <x-rapidez::button href="{{ route('checkout') }}" dusk="checkout" class="w-full text-center">
                            @lang('Checkout')
                        </x-rapidez::button>
                    </div>
                </div>

                <x-rapidez::productlist value="cart.cross_sells" title="More choices to go with your product" field="id" />
            </div>
            <div v-else>
                @lang('You don\'t have anything in your cart.')
            </div>
        </cart>
    </div>
@endsection
