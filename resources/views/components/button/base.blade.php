@props(['tag' => 'button', 'disableWhenLoading' => true])

@php
    $tag = $attributes->hasAny('href', ':href', 'v-bind:href') ? 'a' : $tag;
    $tag = $attributes->has('for') ? 'label' : $tag;
@endphp

<x-rapidez::tag
    is="{{ $tag }}"
    {{ $attributes->merge([
        ':disabled' => $attributes->has('href') || $attributes->has(':href') || !$disableWhenLoading ? null : '$root.loading']) }}
>
    {{ $slot }}
</x-rapidez::tag>
