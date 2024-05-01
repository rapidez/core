{{--
    This is a slideover component using pure CSS, by making use of checkboxes, and
    potentially form reset logic for nested slideovers.

    Properties:
        has-parent:     Used for nested slideovers. Set this to true when this slideover is a child of another slideover.
        id:             Unique identifier for the checkbox input used
        open:           Use this property when you want the slideover to be open by default.
        position:       Position of the slideover on the screen. Can be either `left` or `right`. Defaults to `left`.
        tag:            The base tag of the slideover. Set this to `form` on the parent of a nested slideover. Defaults to `div`.
        title:          A title that gets put into the header. Can be a slot or a property.

    Slots:
        headerbutton:   This slot can be defined if you want a custom button placed in the header instead of the left arrow.
        title:          A title that gets put into the header. Can be a slot or a property.
--}}
@props(['id' => uniqid('slideover-'), 'title', 'hasParent' => false, 'position' => 'left', 'tag' => 'div', 'open' => false])
@slots(['title', 'headerbutton'])

@php
    $isInForm = $tag === 'form' || $hasParent;
    $closeId = $isInForm ? 'close-' . $id : $id;
@endphp

<x-tag v-on:reset="toggleScroll(false)" :is="$tag">
    <input id="{{ 'close-' . $id }}" class="hidden" type="reset">
    @if (!$hasParent)
        <input @checked($open) id="{{ $id }}" v-on:change="toggleScroll($event.target.checked)" class="peer hidden" type="checkbox">
        <label for="{{ $closeId }}" class="pointer-events-none fixed inset-0 z-40 cursor-pointer bg-neutral/50 opacity-0 transition peer-checked:pointer-events-auto peer-checked:opacity-100"></label>
    @else
        <input @checked($open) id="{{ $id }}" class="peer hidden" type="checkbox">
    @endif
    <div {{ $attributes->class([
        'fixed inset-y-0 transition-all bg-white z-40 flex flex-col max-w-md w-full',
        '-right-full peer-checked:right-0' => $position === 'right',
        '-left-full peer-checked:left-0' => $position === 'left',
    ]) }}>
        @include('rapidez::components.slideover.partials.header')
        <div class="slideover-wrapper flex flex-1 flex-col items-start overflow-y-auto bg-inactive-100">
            {{ $slot }}
        </div>
    </div>
</x-tag>
