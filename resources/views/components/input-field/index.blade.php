@props(['label', 'type' => 'text'])
@slots(['input', 'label'])

@php
    $componentType = match($type) {
        'select', 'textarea', 'checkbox' => 'rapidez::' . $type,
        default => 'rapidez::input',
    };
    $labelClasses = match($type) {
        'radio', 'checkbox' => 'flex-row',
        default => '',
    };

    $attributes->offsetSet('type', $type);
    $shifted = ['disabled', 'dusk', 'name', 'placeholder', 'ref', 'required', 'type', 'v-model', 'v-model.lazy'];
@endphp

<x-rapidez::label :attributes="$attributes->except($shifted)->class($labelClasses)" :$label>
    <x-dynamic-component
        :component="$componentType"
        :attributes="$input->attributes->merge($attributes->only($shifted)->getAttributes())"
    >
        {{ $input }}
    </x-dynamic-component>
    {{ $slot }}
</x-rapidez::label>
