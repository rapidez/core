<div class="{{ $wrapperClass ?? '' }}">
    @if(!isset($label) || (isset($label) && $label))
        <x-rapidez::label for="{{ $name }}">@lang($label ?? ucfirst($name))</x-rapidez::label>
    @endif
    @php
        unset($attributes['wrapperClass']);
    @endphp
    <input {{ $attributes->merge(['class' => 'w-full py-2 px-3 border-gray-300 rounded focus:ring-green-500 focus:border-green-500', 'type' => 'text', 'placeholder' => __($placeholder ?? ucfirst($name)), 'dusk' => $attributes->get('v-bind:dusk') ? null : $name, 'id' => $name]) }}>
</div>
