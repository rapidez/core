{{--
This is a special one; $attributes is used twice and should not be used
for styling! This makes sure any data like v-model, min, step, etc
is present on the input and the quantity select component.
--}}
<quantity-select v-slot="qtySelect" {{ $attributes }}>
    <div class="flex border">
        <x-rapidez::button.outline
            v-on:click.prevent="qtySelect.decrease"
            v-bind:disabled="!qtySelect.decreasable"
        >
            <div class="bg-neutral h-0.5 w-3 hover:bg-white"></div>
        </x-rapidez::button.outline>
        <label class="flex items-center">
            <span class="px-2 sr-only">@lang('Quantity')</span>
            <input
                name="qty"
                type="number"
                dusk="qty-select"
                class="w-14 px-1 text-center"
                {{ $attributes }}
            />
        </label>
        <x-rapidez::button.outline
            v-on:click.prevent="qtySelect.increase"
            v-bind:disabled="!qtySelect.increasable"
        >
            <div class="relative">
                <div class="bg-neutral absolute h-0.5 w-3 rotate-90"></div>
                <div class="bg-neutral h-0.5 w-3"></div>
            </div>
        </x-rapidez::button.outline>
    </div>
</quantity-select>
