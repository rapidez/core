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
            <div class="flex flex-col gap-x-10 lg:flex-row">
                <div class="flex w-full flex-col" dusk="cart-content">
                    @include('rapidez::cart.item')

                    <div class="mt-5 self-start">
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

        <div v-if="!hasCart && !$root.loading" v-cloak>@lang('You don\'t have anything in your cart.')</div>
        <div v-if="$root.loading">@lang('Loading')...</div>
    </div>
@endsection
