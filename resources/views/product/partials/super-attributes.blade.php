@foreach ($product->superAttributes ?: [] as $superAttributeId => $superAttribute)
    @php($swatchType = $superAttribute->additional_data['swatch_input_type'] ?? null)
    @if ($swatchType === 'visual')
        @include('rapidez::product.partials.super-attributes.visual-swatch')
    @elseif ($swatchType === 'text')
        @include('rapidez::product.partials.super-attributes.text-swatch')
    @else
        @include('rapidez::product.partials.super-attributes.drop-down')
    @endif
@endforeach
