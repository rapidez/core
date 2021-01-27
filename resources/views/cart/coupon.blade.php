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
                    <button
                        type="submit"
                        class="btn btn-primary ml-3"
                        :disabled="$root.loading"
                    >
                        @lang('Apply')
                    </button>
                </form>
                <p class="text-red-500 text-xs italic w-3/4 mt-3" v-if="submitError">@{{ submitError }}</p>
                <div class="relative rounded-md" v-if="cart.discount_name">
                    <div class="w-100">
                        <button class="inline-block" @click="removeCoupon">
                            <svg class="h-4 w-4 inline-block text-black-400" fill="none" viewBox="0 0 12 12">
                                <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        @lang('Discount'): @{{ cart.discount_name }}
                    </div>
                </div>
            </div>
        </coupon>
    </div>
</div>
