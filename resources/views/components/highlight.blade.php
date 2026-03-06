@props(['attribute', 'item' => 'item'])

<span
    {{ $attributes }}
    v-html="htmlDecode({{ $item }}?._highlightResult?.{{ $attribute }}?.value || {{ $item }}.{{ $attribute }})"
></span>
