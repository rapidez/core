@foreach ($product->super_attributes ?: [] as $superAttributeId => $superAttribute)
    @if ($superAttribute->visual_swatch)
        @include('rapidez::product.partials.super-attributes.visual-swatch')
    @elseif ($superAttribute->text_swatch)
        @include('rapidez::product.partials.super-attributes.text-swatch')
    @else
        @include('rapidez::product.partials.super-attributes.drop-down')
    @endif
@endforeach
