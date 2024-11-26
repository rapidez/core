{{--
This is a wrapper for the global slideover. Use this as much as possible to reduce the amount of html elements in the DOM.

Standard usage may look like this:
```
<x-rapidez::slideover.global :title="__('My slideover')">
    <x-slot:label class="rounded p-3 border">
        Click me
    </x-slot:label>

    <div class="font-semibold">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
    </div>
</x-rapidez::slideover.global>
```
--}}

@props(['title', 'position' => 'left'])
@slots(['label', 'contents'])

<global-slideover title="{{ $title }}" position="{{ $position }}" contents="{{ $slot->toHtml() }}" v-slot="slideover">
    <label {{ $label->attributes->class('global-slideover-label cursor-pointer') }} for="slideover-global" v-on:click="slideover.open">
        {{ $label }}
    </label>
</global-slideover>
