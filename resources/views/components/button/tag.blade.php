@props(['tag' => 'button', 'disableWhenLoading' => true, 'loader' => false])

@php
    $tag = $attributes->hasAny('href', ':href', 'v-bind:href') ? 'a' : $tag;
    $tag = $attributes->has('for') ? 'label' : $tag;
@endphp

<x-rapidez::tag
    is="{{ $tag }}"
    :v-bind:class="$loader ? '{ \'button-loading\': loading.value }' : null"
    {{ $attributes->merge([
        'v-bind:disabled' => $attributes->hasAny('href', ':href', ':disabled') || !$disableWhenLoading ? null : 'loading.value',
    ]) }}
>
    {{ $slot }}
</x-rapidez::tag>
