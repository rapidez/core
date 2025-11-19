@props(['attribute', 'item' => 'item', 'highlightTag' => 'mark'])

@if (in_array($attribute, config('rapidez.searchkit.highlight_attributes')))
    <ais-highlight {{ $attributes->merge([
        'v-bind:hit' => $item,
        'attribute' => $attribute,
        'highlighted-tag-name' => $highlightTag,
    ]) }}></ais-highlight>
    {{--
    TODO: This doesn't render the HTML correctly
    See: /gear/fitness-equipment.html
    --}}
@else
    <span {{ $attributes }} v-text="{{ $item }}.{{ $attribute }}"></span>
@endif
