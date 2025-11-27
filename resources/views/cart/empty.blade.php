<div class="bg rounded-md mt-6 p-10 mb-6">
    <x-rapidez-empty-cart class="w-full flex items-center justify-center h-36" />
</div>
<div class="flex flex-col items-center justify-center text-center text-secondary mb-10 text-2xl">
    @lang('You don\'t have anything in your cart.')
    <div class="flex flex-wrap gap-3 mt-3">
        <x-rapidez::button.secondary  v-if="!$root.loggedIn" href="{{ route('account.login') }}">
            @lang('Login to view your shopping cart')
        </x-rapidez::button.secondary>
        <x-rapidez::button.outline href="{{ url('/') }}">
            @lang('Continue shopping')
        </x-rapidez::button.outline>
    </div>
</div>

@include('rapidez::listing.partials.popular-products')
