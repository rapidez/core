@props(['name', 'label', 'type' => 'text', 'disabled' => false, 'required' => false, 'placeholder' => false, 'dusk' => null])
@slots(['input', 'label'])

@php
    $componentType = match($type) {
        'select', 'textarea', 'checkbox' => 'rapidez::' . $type,
        default => 'rapidez::input',
    };
    $labelClasses = match($type) {
        'radio', 'checkbox' => '!flex-row',
        default => '',
    }
@endphp

<x-rapidez::label :attributes="$attributes->class($labelClasses)" :$label>
    <x-dynamic-component :component="$componentType" :attributes="$input->attributes->merge([
        'name' => $name ?? null,
        'type' => $type,
        'placeholder' => $placeholder,
        'dusk' => $dusk,
        'disabled' => $disabled,
        'required' => $required,
    ])">
        {{ $input }}
    </x-dynamic-component>
    {{ $slot }}
</x-rapidez::label>
