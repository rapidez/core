@props(['label', 'type' => 'text', 'srOnlyLabel' => false])
@slots(['input', 'label'])

@php
    $shifted = config('rapidez.frontend.input-field.shifted');
    $attributes->offsetSet('type', $type);
    $componentType = match($type) {
        'select', 'textarea', 'checkbox', 'radio' => 'rapidez::' . $type,
        default => 'rapidez::input',
    };
@endphp

<x-rapidez::label
    :attributes="$attributes
        ->except($shifted)
        ->whereDoesntStartWith('v-bind:')
        ->twMerge(in_array($type, ['radio', 'checkbox']) ? 'flex-row' : '')"
    :$label
    :$srOnlyLabel
>
    <x-dynamic-component
        :component="$componentType"
        :attributes="$inputAttributes = $input->attributes
            ->merge($attributes->only($shifted)->getAttributes(), false)
            ->merge($attributes->whereStartsWith('v-bind:')->getAttributes(), false)"
    >
        {{ $input }}
    </x-dynamic-component>
    {{ $slot }}
</x-rapidez::label>