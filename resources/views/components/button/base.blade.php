@props(['disableWhenLoading' => true])

@php($tag = match(true) {
    $isAnchor = $attributes->hasAny('href', ':href', 'v-bind:href') => 'a',
    $isLabel = $attributes->has('for') => 'label',
    default => 'button',
})

<x-tag is="{{ $tag }}" {{ $attributes
    ->twMerge('flex items-center justify-center font-semibold py-2 px-4 border rounded disabled:opacity-50 disabled:cursor-not-allowed hover:opacity-75 whitespace-nowrap transition')
    ->merge([':disabled' => $isAnchor || !$disableWhenLoading ? null : '$root.loading' ]) }}>
    {{ $slot }}
</x-tag>
