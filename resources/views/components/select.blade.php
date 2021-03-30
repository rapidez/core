@if(!isset($label) || (isset($label) && $label))
    <x-rapidez::label for="{{ $name ?? '' }}">
        @lang($label ?? ucfirst($name ?? ''))
    </x-rapidez::label>
@endif
<select {{ $attributes->merge([
    'id' => $name ?? null,
    'name' => $name ?? null,
    'dusk' => $attributes->get('v-bind:dusk') ? null : $name ?? null,
    'class' => $class ?? 'py-2 pl-3 pr-8 border-gray-300 rounded focus:ring-green-500 focus:border-green-500 sm:text-sm'
]) }}>
    {{ $slot }}
</select>
