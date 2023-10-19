<x-rapidez::label for="option_{{ $option->option_id }}">
    <price :product="simpleProduct">
        <template slot-scope="{ calculatePrice }">
            {{ $option->title }}
            @if($option->price->price_type !== 'percent')
                + @{{ window.price(calculatePrice(simpleProduct, 'catalog', {price: @php echo $option->price->price @endphp } )) }}
            @else
                {{ $option->price_label }}
            @endif
            </x-rapidez::select>
        </template>
    </price>
</x-rapidez::label>
<x-rapidez::input
    :label="false"
    :name="false"
    id="option_{{ $option->option_id }}"
    :required="$option->is_require"
    :maxlength="$option->max_characters ?: false"
    v-model="customOptions[{{ $option->option_id }}]"
/>

