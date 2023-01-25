@props(['variant' => 'primary', 'disableWhenLoading' => true])

@php
$tag = $attributes->has('href') || $attributes->has(':href') ? 'a' : 'button';
$baseClasses[] = 'inline-block font-semibold py-2 px-4 border rounded disabled:opacity-50 disabled:cursor-not-allowed hover:opacity-75 whitespace-nowrap transition';
$variants = [
    'primary' => 'bg-primary border-primary text-white',
    'outline' => 'bg-transparent hover:bg-primary text-primary hover:text-white border-primary hover:border-transparent',
    'slider' => ['flex items-center justify-center rounded-full w-12 h-12 bg-white border hover:bg-primary hover:text-white'],
];
@endphp

<{{ $tag }} {{ $attributes->merge([
    'type' => $attributes->has('href') || $attributes->has(':href') ? null : 'button',
    'class' => implode(' ', isset($variants[$variant])
        ? (is_array($variants[$variant])
            ? $variants[$variant]
            : array_merge($baseClasses, [$variants[$variant]]))
        : $baseClasses),
    ':disabled' => $attributes->has('href') || $attributes->has(':href') || !$disableWhenLoading ? null : '$root.loading']) }}>
    {{ $slot }}
</{{ $tag }}>
