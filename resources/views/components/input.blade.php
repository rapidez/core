@props(['name', 'label', 'wrapperClass', 'labelClass'])

<div class="{{ $wrapperClass ?? '' }}">
    @if (!isset($label) || (isset($label) && $label))
        <x-rapidez::label for="{{ $name }}" class="{{ $labelClass ?? '' }}">
            @lang($label ?? ucfirst($name))
        </x-rapidez::label>
    @endif
    <input
        {{ $attributes->merge([
            'id' => $name,
            'name' => $name,
            'type' => 'text',
            'placeholder' => __($placeholder ?? ''),
            'dusk' => $attributes->get('v-bind:dusk') ? null : $name,
            'class' => 'w-full py-2 px-3 border-neutral-110 rounded !ring-0 focus:!border-inactive sm:text-sm text-neutral',
        ]) }}
    >
</div>
