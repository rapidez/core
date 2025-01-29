{{--
This is a special one; $attributes is used twice and should not be used
for styling! This makes sure any data like v-model, min, step, etc
is present on the input and the quantity select component.
--}}
<quantity-select v-slot="qtySelect" {{ $attributes }}>
    <div class="flex items-center justify-center border rounded bg-white h-12 self-start">
        <button
            disabled
            v-on:click.prevent="qtySelect.decrease"
            v-bind:disabled="!qtySelect.decreasable"
            aria-label="@lang('Decrease')"
            class="shrink-0 pl-2.5 text disabled:cursor-not-allowed disabled:opacity-50"
        >
            <x-heroicon-o-minus class="mt-0.5 size-5" stroke-width="2" />
        </button>
        <input
            name="qty"
            type="number"
            dusk="qty"
            value="1"
            class="outline-0 ring-0 border-none w-12 bg-transparent font-medium text-center px-0 sm:text-base focus:ring-transparent"
            aria-label="@lang('Quantity')"
            {{ $attributes }}
        />
        <button
            v-on:click.prevent="qtySelect.increase"
            v-bind:disabled="!qtySelect.increasable"
            aria-label="@lang('Increase')"
            class="shrink-0 pr-2.5 text disabled:cursor-not-allowed disabled:opacity-50"
        >
            <x-heroicon-o-plus class="mt-0.5 size-5" stroke-width="2" />
        </button>
    </div>
</quantity-select>
