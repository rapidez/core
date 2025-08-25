<div>
    <x-rapidez::label>
        {{ $superAttribute->label }}
    </x-rapidez::label>

    <ul class="flex flex-wrap gap-x-1.5 gap-y-2 items-center pr-14">
        @foreach ($product->{'super_' . $superAttribute->code} as $optionId => $option)
            <li>
                <x-rapidez::input.swatch.visual
                    type="radio"
                    name="{{ $superAttribute->code }}"
                    v-model="addToCart.options[{{ $superAttributeId }}]"
                    v-bind:disabled="addToCart.disabledOptions.super_{{ $superAttribute->code }}.includes({{ $option->value }})"
                    :value="$option->value"
                    :aria-label="$option->label"
                    :id="$option->label"
                    :color="config('rapidez.models.option_swatch')::getCachedSwatchValue($superAttribute->code, $option->value)['swatch'] ?? 'none'"
                    static-color
                    required
                >
                    {{ $option->label }}
                </x-rapidez::input.swatch.visual>
            </li>
        @endforeach
    </ul>
</div>
