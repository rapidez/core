@props(['label', 'type' => 'text', 'srOnlyLabel' => false])
@slots(['input', 'label'])

@php
    $shifted = ['disabled', 'dusk', 'name', 'placeholder', 'ref', 'required', 'type', 'v-model', 'v-model.lazy'];
    $attributes->offsetSet('type', $type);
    $componentType = match($type) {
        'select', 'textarea', 'checkbox' => 'rapidez::' . $type,
        default => 'rapidez::input',
    };
    $labelClasses = match($type) {
        'radio', 'checkbox' => 'flex-row',
        default => '',
    };
@endphp

<x-rapidez::label :attributes="$attributes->except($shifted)->class($labelClasses)" :$label :$srOnlyLabel>
    <x-dynamic-component
        :component="$componentType"
        :attributes="$input->attributes->merge($attributes->only($shifted)->getAttributes())"
    >
        {{ $input }}
    </x-dynamic-component>
    {{ $slot }}
</x-rapidez::label>
