@foreach ($product->super_attributes ?: [] as $superAttributeId => $superAttribute)
    @if ($superAttribute->visual_swatch)
        @include('rapidez::product.partials.super-attributes.visual_swatch')
    @elseif ($superAttribute->text_swatch)
        @include('rapidez::product.partials.super-attributes.text_swatch')
    @else
        @include('rapidez::product.partials.super-attributes.drop_down')
    @endif
@endforeach
