@props(['attribute', 'item' => 'item'])

<span
    {{ $attributes }}
    v-html="{{ $item }}?._highlightResult?.{{ $attribute }}?.value ? htmlDecode({{ $item }}?._highlightResult?.{{ $attribute }}?.value) : {{ $item }}.{{ $attribute }}"
></span>
