@props(['disableWhenLoading' => true])

@php
    $tag = $attributes->has('href', ':href', 'v-bind:href') ? 'a' : 'button';
    $tag = $attributes->has('for') ? 'label' : $tag;
@endphp

<component
    is="{{ $tag }}"
    {{ $attributes->merge([
        'class' => 'flex items-center justify-center font-semibold py-2 px-4 border rounded disabled:opacity-50 disabled:cursor-not-allowed hover:opacity-75 whitespace-nowrap transition',
        ':disabled' => $attributes->has('href') || $attributes->has(':href') || !$disableWhenLoading ? null : '$root.loading' ]) }}
>
    {{ $slot }}
</component>