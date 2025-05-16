@include('rapidez::cart.partials.summary')

<div class="w-full mt-4" :class="{ 'cursor-not-allowed': !canOrder }">
    <x-rapidez::button.conversion
        href="{{ route('checkout') }}"
        class="w-full text-center"
        v-bind:class="{ 'pointer-events-none': !canOrder }"
        dusk="checkout"
    >
        @lang('Checkout')
    </x-rapidez::button.conversion>
</div>
