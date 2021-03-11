@props(['name', 'label', 'wrapperClass'])

<div class="{{ $wrapperClass ?? '' }}">
    @if(!isset($label) || (isset($label) && $label))
        <x-rapidez::label for="{{ $name }}">
            @lang($label ?? ucfirst($name))
        </x-rapidez::label>
    @endif
    <input {{ $attributes->merge([
        'id' => $name,
        'name' => $name,
        'type' => 'text',
        'placeholder' => __($placeholder ?? ucfirst($name)),
        'dusk' => $attributes->get('v-bind:dusk') ? null : $name,
        'class' => 'w-full py-2 px-3 border-gray-300 rounded focus:ring-green-500 focus:border-green-500',
    ]) }}>
</div>
