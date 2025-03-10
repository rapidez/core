@props(['attribute', 'item' => 'item'])

@if (in_array($attribute, config('rapidez.searchkit.highlight_attributes')))
    <ais-highlight
        {{ $attributes }}
        attribute="{{ $attribute }}"
        :hit="{{ $item }}"
        highlighted-tag-name="mark"
    />
@else
    <span {{ $attributes }} v-text="{{ $item }}.{{ $attribute }}"></span>
@endif
