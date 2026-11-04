@extends('rapidez::layouts.app')

@section('title', __('Cart'))

@section('robots', 'NOINDEX,NOFOLLOW')

@section('content')
    <div class="container">
        <h1 class="mb-5 text-4xl font-bold">@lang('Cart')</h1>
        <graphql
            v-if="mask?.value"
            :query="'query getCart($cart_id: String!) { cart (cart_id: $cart_id) { ...cart } } ' + config.fragments.cart"
            :variables="{ cart_id: mask.value }"
            :callback="updateCart"
            :error-callback="checkResponseForExpiredCart"
        >
        </graphql>
        <div v-if="hasCart.value" v-cloak>
            <div v-if="!canOrder.value" class="bg-red-700/10 text-red-700 flex gap-1 w-fit px-2 py-1 rounded mb-2">
                <x-heroicon-o-exclamation-circle class="size-5 mt-0.5 shrink-0" />
                <template v-if="cart.value.items.filter(item => !item.is_available || item.errors?.length).length > 1">
                    @lang(':count items need your attention before you can check out.', ['count' => '@{{ cart.value.items.filter(item => !item.is_available || item.errors?.length).length }}'])
                </template>
                <template v-else>
                    @lang('1 item needs your attention before you can check out.')
                </template>
            </div>
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
                value="cart.value.items.flatMap((item) => item.product.crosssell_products.map((crosssell) => crosssell.id))"
                title="More choices to go with your product"
                field="entity_id"
            />
        </div>

        <div v-if="!hasCart && !loading.value" v-cloak>@lang('You don\'t have anything in your cart.')</div>
        <div v-if="loading.value">@lang('Loading')...</div>
    </div>
@endsection
