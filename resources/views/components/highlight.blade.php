@props(['attribute', 'item' => 'item'])

<span
    {{ $attributes }}
    v-html="{{ $item }}?._highlightResult?.{{ $attribute }}?.value ? stripHtmlTags(htmlDecode({{ $item }}?._highlightResult?.{{ $attribute }}?.value)) : stripHtmlTags({{ $item }}.{{ $attribute }} ?? '')"
></span>
