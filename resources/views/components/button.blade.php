@props(['variant' => 'primary'])

@php
$tag = $attributes->has('href') ? 'a' : 'button';
$classes[] = 'inline-block font-semibold py-2 px-4 border rounded disabled:opacity-50 disabled:cursor-not-allowed hover:opacity-75';
if ($variant == 'primary') {
    $classes[] = 'bg-primary border-primary text-white';
}
if ($variant == 'outline') {
    $classes[] = 'bg-transparent hover:bg-primary text-primary hover:text-white border-primary hover:border-transparent';
}
@endphp

<{{ $tag }} {{ $attributes->merge([
    'type' => $attributes->has('href') ? null : 'button',
    'class' => implode(' ', $classes),
    ':disabled' => $attributes->has('href') ? null : '$root.loading']) }}>
    {{ $slot }}
</{{ $tag }}>
