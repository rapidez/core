<div class="bg-white rounded-lg border p-3">
    <x-rapidez::title tag="h3" class="text-lg leading-6 font-medium text-gray-900 mb-5">
        @lang('Apply coupon code')
    </x-rapidez::title>

    @include('rapidez::cart.coupon.add')
    @include('rapidez::cart.coupon.list')
</div>
