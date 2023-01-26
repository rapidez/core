@props(['name', 'label', 'wrapperClass', 'labelClass'])

<div class="{{ $wrapperClass ?? '' }}">
    @if (!isset($label) || (isset($label) && $label))
        <x-rapidez::label
            class="{{ $labelClass ?? '' }} mb-2 block text-secondary"
            for="{{ $name }}"
        >
            @lang($label ?? ucfirst($name))
        </x-rapidez::label>
    @endif
    <input
        {{ $attributes->merge([
            'id' => $name,
            'name' => $name,
            'type' => 'text',
            'placeholder' => __($placeholder ?? ucfirst($name)),
            'dusk' => $attributes->get('v-bind:dusk') ? null : $name,
            'class' => 'w-full py-2 px-3 border-border rounded !ring-0 focus:!border-secondary sm:text-sm text-primary',
        ]) }}
    >
</div>
