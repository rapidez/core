@php($checkoutAgreements = \Rapidez\Core\Models\CheckoutAgreement::all())

<h1 class="font-bold text-4xl mb-5">@lang('Payment method')</h1>
<form class="bg-inactive-100 p-8 rounded mt-6" v-on:submit.prevent="save(['payment_method'], 4)">
    <div class="my-2 border bg-white p-4 rounded" v-for="(method, index) in checkout.payment_methods">
        <x-rapidez::radio
            v-bind:value="method.code"
            v-bind:dusk="'method-'+index"
            v-model="checkout.payment_method"
            class="[&+div]:flex-1"
        >
            <div class="flex items-center">
                <span>@{{ method.title }}</span>
                <img
                    class="ml-auto w-8 h-8"
                    v-bind:src="`/payment-icons/${method.code}.svg`"
                    onerror="this.onerror=null; this.src=`/payment-icons/default.svg`"
                >
            </div>
        </x-rapidez::radio>
    </div>

    @if (count($checkoutAgreements))
        <div>
            @foreach ($checkoutAgreements as $agreement)
                <x-rapidez::slideover position="right" id="{{ $agreement->agreement_id }}_agreement">
                    <x-slot name="title">
                        {{ $agreement->name }}
                    </x-slot>

                    @if ($agreement->is_html)
                        <div>
                            {!! $agreement->content !!}
                        </div>
                    @else
                        <div class="whitespace-pre-line">
                            {{ $agreement->content }}
                        </div>
                    @endif
                </x-rapidez::slideover>

                @if ($agreement->mode == 'AUTO')
                    <label class="text-gray-700 cursor-pointer underline hover:no-underline" for="{{ $agreement->agreement_id }}_agreement">
                        {{ $agreement->checkbox_text }}
                    </label>
                @else
                    <div>
                        <x-rapidez::checkbox
                            name="agreement_ids[]"
                            :value="$agreement->agreement_id"
                            v-model="checkout.agreement_ids"
                            dusk="agreements"
                            required
                        >
                            <label class="text-gray-700 cursor-pointer underline hover:no-underline" for="{{ $agreement->agreement_id }}_agreement">
                                {{ $agreement->checkbox_text }}
                            </label>
                        </x-rapidez::checkbox>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <x-rapidez::button type="submit" class="mt-5" dusk="continue">
        @lang('Place order')
    </x-rapidez::button>
</form>
