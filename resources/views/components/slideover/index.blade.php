@props(['id', 'title', 'hasParent' => false, 'right' => false, 'isDiv' => false])
@slots(['title', 'headerbutton'])

@php
    $isInForm = !$isDiv || $hasParent;
    $closeId = $isInForm ? 'close-' . $id : $id;
@endphp

<x-tag v-on:reset="toggleScroll(false)" :is="$isDiv ? 'div' : 'form'">
    <input id="{{ $id }}" v-on:change="toggleScroll($event.target.checked)" class="peer hidden" type="checkbox">
    <input id="{{ 'close-' . $id }}" class="hidden" type="reset">
    @if (!$hasParent)
        <label for="{{ $closeId }}" class="pointer-events-none fixed inset-0 z-40 cursor-pointer bg-neutral/50 opacity-0 transition peer-checked:pointer-events-auto peer-checked:opacity-100"></label>
    @endif
    <div {{ $attributes->class([
        'fixed inset-y-0 transition-all bg-white z-40 flex flex-col max-w-md w-full',
        $right ? '-right-full peer-checked:right-0' : '-left-full peer-checked:left-0',
    ]) }}>
        @include('rapidez::components.slideover.partials.header')
        <div class="flex flex-1 flex-col items-start overflow-y-auto bg-inactive-100">
            {{ $slot }}
        </div>
    </div>
</x-tag>
