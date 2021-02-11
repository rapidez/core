@extends('rapidez::layouts.app')

@section('title', 'Cart')

@section('content')
    <h1 class="font-bold text-4xl mb-5">@lang('Cart')</h1>
        <cart>
        <graphql slot-scope="{ changeQty, remove }" v-cloak query='@include('rapidez::cart.queries.cart')' >
            <div v-if="data" slot-scope="{ data, runQuery }">
                <div class="flex flex-wrap items-center border-b pb-2 mb-2" v-for="(item, productId, index) in data.cart.items">
                    <div class="w-1/6 sm:w-1/12 pr-3">
                        <a :href="item.url" class="block">
                            <img
                                :alt="item.product.name"
                                :src="item.product.image.url"
                                width="100"
                            />
                        </a>
                    </div>
                    <div class="w-5/6 sm:w-5/12 lg:w-8/12">
                    <a :href="item.url" class="font-bold">@{{ item.product.name }}</a>
                    <div v-for="(optionValue, option) in item.options">
                        @{{ option }}: @{{ optionValue }}
                    </div>
                </div>
                <div class="w-2/6 sm:w-1/6 lg:w-1/12 text-right pr-5">
                    @{{ item.prices.price.value | price }}
                </div>
                <div class="w-2/6 sm:w-1/6 lg:w-1/12">
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
                        <button
                            v-on:click="changeQty(item); runQuery()"
                            class="btn btn-primary ml-1"
                            :disabled="$root.loading"
                            title="@lang('Update')"
                            :dusk="'item-update-'+index"
                        >
                            <svg class="fill-current w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6 18.7V21a1 1 0 0 1-2 0v-5a1 1 0 0 1 1-1h5a1 1 0 1 1 0 2H7.1A7 7 0 0 0 19 12a1 1 0 1 1 2 0 9 9 0 0 1-15 6.7zM18 5.3V3a1 1 0 0 1 2 0v5a1 1 0 0 1-1 1h-5a1 1 0 0 1 0-2h2.9A7 7 0 0 0 5 12a1 1 0 1 1-2 0 9 9 0 0 1 15-6.7z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="w-2/6 sm:w-1/6 lg:w-1/12 flex justify-end text-right">
                    @{{ item.prices.price.value * item.quantity | price }}
                    <a href="#" @click.prevent="remove(item); runQuery()" class="ml-2" title="@lang('Remove')" :dusk="'item-delete-'+index">
                        <svg class="fill-current w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path class="heroicon-ui" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2zm0 2v14h14V5H5zm8.41 7l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z"/></svg>
                    </a>
                </div>
                </div>
            <div class="sm:flex sm:justify-between sm:items-start mt-5">
                @include('rapidez::cart.coupon')
                <div class="flex flex-wrap justify-end sm:w-64">
                    <div class="flex flex-wrap w-full p-3 mb-5 bg-secondary rounded-lg">
                        <div class="w-1/2">@lang('Subtotal'):</div>
                        <div class="w-1/2 text-right">@{{ data.cart.prices.subtotal_excluding_tax.value | price }}</div>
                        <div v-if="data.cart.prices.applied_taxes.length" class="w-1/2">@lang('Tax'):</div>
                        <div v-if="data.cart.prices.applied_taxes.length" v-for="(tax) in data.cart.prices.applied_taxes" class="w-1/2 text-right">@{{ tax | price }}</div>

                      <div class="w-1/2" v-if="data.cart.prices.discounts.length" v-for="(discount) in data.cart.prices.discounts">
                          @{{ discount.label }}:
                          </div>
                            <div class="w-1/2 text-right" v-if="data.cart.prices.discounts.length" v-for="(discount) in data.cart.prices.discounts">
                                @{{ discount.amount.value | price }}
                          </div>
                        <div class="w-1/2 font-bold">@lang('Total'):</div>
                        <div class="w-1/2 text-right font-bold">@{{ data.cart.prices.grand_total.value | price }}</div>
                      </div>
                        <a href="/checkout" class="btn btn-primary" dusk="checkout">@lang('Checkout')</a>
                    </div>
                </div>
            </div>
        </div>
        </graphql>
        </cart>
@endsection
