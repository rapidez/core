@props(['name', 'label', 'wrapperClass', 'labelClass'])

<div class="{{ $wrapperClass ?? '' }}">
    @if(!isset($label) || (isset($label) && $label))
        <x-rapidez::label for="{{ $name ?? '' }}" class="{{ $labelClass ?? '' }}">
            @lang($label ?? ucfirst($name ?? ''))
        </x-rapidez::label>
    @endif
    <select {{ $attributes->merge([
        'id' => $name ?? null,
        'name' => $name ?? null,
        'dusk' => $attributes->get('v-bind:dusk') ? null : $name ?? null,
        'class' => 'w-full py-2 pl-3 pr-8 border-gray-200 rounded focus:ring-primary focus:border-primary sm:text-sm'
    ]) }}>
        {{ $slot }}
    </select>
</div>
