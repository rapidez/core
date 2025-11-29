@extends('rapidez::layouts.app')

@section('title', __('Cart'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <h1 class="mb-5 text-4xl font-bold">@lang('Cart')</h1>
        <graphql
            v-if="mask"
            :query="'query getCart($cart_id: String!) { cart (cart_id: $cart_id) { ...cart } } ' + config.fragments.cart"
            :variables="{ cart_id: mask }"
            :callback="updateCart"
            :error-callback="checkResponseForExpiredCart"
        >
        </graphql>
        <div v-if="hasCart" v-cloak>
            <div class="flex gap-x-10 mb-8 max-lg:flex-col">
                <div class="flex w-full flex-col">
                    @include('rapidez::cart.item')

                    <div class="mt-5 lg:self-start">
                        @include('rapidez::cart.coupon')
                    </div>
                </div>

                <div class="flex w-full flex-wrap justify-end self-baseline max-lg:mt-5 lg:max-w-xs">
                    @include('rapidez::cart.sidebar')
                </div>
            </div>

            <x-rapidez::productlist
                value="cart.items.flatMap((item) => item.product.crosssell_products.map((crosssell) => crosssell.id))"
                title="More choices to go with your product"
                field="entity_id"
            />
        </div>

        <div v-if="!hasCart && !loading" v-cloak>
            @include('rapidez::cart.empty')
        </div>
        <div v-if="loading">@lang('Loading')...</div>
    </div>
@endsection
