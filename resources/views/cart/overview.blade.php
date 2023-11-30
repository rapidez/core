@extends('rapidez::layouts.app')

@section('title', __('Cart'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <h1 class="mb-5 text-4xl font-bold">@lang('Cart')</h1>

        {{-- TODO: Test the whole thing when a user is logged in --}}
        <graphql
            v-if="window.app.mask"
            :query="'query getCart($cart_id: String!) { cart (cart_id: $cart_id) { ' + config.queries.cart + ' } }'"
            :variables="{ cart_id: window.app.mask }"
            :callback="refreshCart"
            v-cloak
        >
            <div>
                <div v-if="hasCart">
                    <div class="flex flex-col gap-x-10 lg:flex-row">
                        <div class="flex w-full flex-col">
                            @include('rapidez::cart.item')

                            <div class="mt-5 self-start">
                                @include('rapidez::cart.coupon')
                            </div>
                        </div>

                        <div class="flex w-full flex-wrap justify-end self-baseline lg:max-w-xs">
                            @include('rapidez::cart.sidebar')
                        </div>
                    </div>
                    <x-rapidez::productlist
                        value="[...new Set(cart.items.map((item) => item.product.crosssell_products.map((crosssell) => crosssell.id)).flat())]"
                        title="More choices to go with your product"
                        field="id"
                    />
                </div>
            </div>
        </graphql>

        <div v-if="!hasCart && !$root.loading" v-cloak>@lang('You don\'t have anything in your cart.')</div>
        <div v-if="$root.loading">@lang('Loading')...</div>
    </div>
@endsection
