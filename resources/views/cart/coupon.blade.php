<div class="bg-white rounded-lg border sm:w-80 mb-3">
    <div class="p-3">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            @lang('Apply coupon code')
        </h3>
        <coupon v-slot="{ cart, removeCoupon, couponCode, inputEvents, applyCoupon, submitError }">
            <div>
                <form class="mt-5 flex" @submit.prevent="applyCoupon">
                    <x-rapidez::input
                        :label="false"
                        name="couponCode"
                        placeholder="Coupon code"
                        v-on="inputEvents"
                        v-bind:value="couponCode"
                        v-bind:disabled="$root.loading"
                    />

                    <x-rapidez::button type="submit" class="ml-3 sm:text-sm">
                        @lang('Apply')
                    </x-rapidez::button>
                </form>
                <p class="text-red-500 text-xs italic w-3/4 mt-3" v-if="submitError">@{{ submitError }}</p>
                <div class="relative rounded-md" v-if="cart.discount_name && cart.discount_amount > 0">
                    <div class="flex items-center">
                        <button v-on:click="removeCoupon">
                            <x-heroicon-s-x class="h-4 w-4 text-black-400"/>
                        </button>
                        @lang('Discount'): @{{ cart.discount_name }}
                    </div>
                </div>
            </div>
        </coupon>
    </div>
</div>
