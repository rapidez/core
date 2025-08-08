@props(['tag' => 'button', 'disableWhenLoading' => true, 'loader' => false])

@php
    $tag = $attributes->hasAny('href', ':href', 'v-bind:href') ? 'a' : $tag;
    $tag = $attributes->has('for') ? 'label' : $tag;

    if ($loader) {
        $attributes['v-bind:disabled'] = 'loading';
    }
@endphp

<x-rapidez::tag
    is="{{ $tag }}"
    {{ $attributes->merge([
        ':disabled' => $attributes->has('href') || $attributes->has(':href') || !$disableWhenLoading ? null : '$root.loading',
    ]) }}
>
    <span class="contents" @if ($loader) v-bind:class="{'invisible': loading}" @endif>
        {{ $slot }}
    </span>

    @if ($loader)
        <div v-if="loading" class="absolute right-1/2 bottom-1/2 translate-x-1/2 translate-y-1/2" v-cloak>
            <x-heroicon-o-arrow-path class="animate-spin size-5"/>
        </div>
    @endif
</x-rapidez::tag>