@props(['attribute', 'item' => 'item'])

<span
    {{ $attributes }}
    v-html="window.stripHtmlTags(window.htmlDecode({{ $item }}?._highlightResult?.{{ $attribute }}?.value || {{ $item }}.{{ $attribute }}))"
></span>
