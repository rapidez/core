<x-rapidez::label for="option_{{ $option->option_id }}">
    {{ $option->title }}
</x-rapidez::label>
<price :product="simpleProduct">
    <template slot-scope="{ calculatePrice }">
        <x-rapidez::select
            :label="false"
            id="option_{{ $option->option_id }}"
            :required="$option->is_require"
            v-model="customOptions[{{ $option->option_id }}]"
        >
            <option selected @if($option->is_require) disabled @endif :value="undefined">@lang('Select')</option>
            @foreach($option->values as $value)
                <option value="{{ $value->option_type_id }}">
                    {{ $value->title }}
                    @if($value->price->price_type !== 'percent')
                        + @{{ window.price(calculatePrice(simpleProduct, 'catalog', {price: @php echo $value->price->price @endphp } )) }}
                    @else
                        {{ $value->price_label }}
                    @endif
                </option>
            @endforeach
        </x-rapidez::select>
    </template>
</price>
