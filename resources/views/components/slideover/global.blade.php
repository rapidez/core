{{--
    This is a wrapper for the global slideover. Use this as much as possible to reduce the amount of html elements in the DOM.

    Standard usage may look like this:
    <x-rapidez::slideover.global :title="__('My slideover')">
        <x-slot:label class="rounded p-3 border">
            Click me
        </x-slot:label>

        <div class="font-semibold">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </div>
    </x-rapidez::slideover.global>
--}}

@props(['title', 'position' => 'left'])
@slots(['label', 'contents'])

<global-slideover title="{{ $title }}" position="{{ $position }}" contents="{{ $slot->toHtml() }}" v-slot="slideover">
    <label {{ $label->attributes->class('global-slideover-label cursor-pointer') }} for="slideover-global" v-on:click="slideover.open">
        {{ $label }}
    </label>
</global-slideover>
