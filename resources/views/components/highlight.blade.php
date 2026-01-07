@props(['attribute', 'item' => 'item'])

<span
    {{ $attributes }}
    v-html="stripHtmlTags(htmlDecode({{ $item }}?._highlightResult?.{{ $attribute }}?.value || {{ $item }}.{{ $attribute }}))"
></span>
