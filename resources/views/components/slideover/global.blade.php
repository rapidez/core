{{--
This is a wrapper for the global slideover. Use this as much as possible to reduce the amount of html elements in the DOM.
Standard usage may look like this:
```
<x-rapidez::slideover.global title="My slideover">
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
@slots(['label'])

<global-slideover title="{{ $title }}" position="{{ $position }}" v-slot="slideover">
    <div>
        <label {{ $label->attributes->class('global-slideover-label cursor-pointer') }} v-on:click="slideover.open">
            {{ $label }}
        </label>
        <div class="hidden">
            <teleport to="#global-slideover-content" :disabled="!slideover.isCurrentSlideover">
                <div>{{ $slot }}</div>
            </teleport>
        </div>
    </div>
</global-slideover>
