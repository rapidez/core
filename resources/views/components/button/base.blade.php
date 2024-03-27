@props(['disableWhenLoading' => true])

@php
    $tag = $attributes->hasAny('href', ':href', 'v-bind:href') ? 'a' : 'button';
    $tag = $attributes->has('for') ? 'label' : $tag;
@endphp

<x-tag
    is="{{ $tag }}"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center font-semibold gap-x-2 transition disabled:opacity-50 disabled:cursor-not-allowed',
        ':disabled' => $attributes->has('href') || $attributes->has(':href') || !$disableWhenLoading ? null : '$root.loading' ]) }}
>
    {{ $slot }}
</x-tag>
