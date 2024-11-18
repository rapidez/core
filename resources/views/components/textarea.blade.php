@props(['name', 'label', 'wrapperClass'])

<div class="{{ $wrapperClass ?? '' }}">
    @if (!isset($label) || (isset($label) && $label))
        <x-rapidez::label for="{{ $name }}" class="{{ $labelClass ?? '' }} mb-2 block text-inactive">
            @lang($label ?? ucfirst($name))
        </x-rapidez::label>
    @endif
    <textarea {{ $attributes->merge([
        'id' => $name,
        'name' => $name,
        'placeholder' => __($placeholder ?? ucfirst($name)),
        'dusk' => $attributes->get('v-bind:dusk') ? null : $name,
        'class' => 'w-full py-2 px-3 border-neutral-110 rounded !ring-0 focus:!border-inactive sm:text-sm text-neutral',
    ]) }}></textarea>
</div>
