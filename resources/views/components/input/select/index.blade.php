{{--
Examples:

1. Just a select:
```
<x-rapidez::select name="something">
    <option value="1">Option 1</option>
</x-rapidez::select>
```

2. With a label:
```
<label>
    <x-rapidez::input.label>@lang('Something')</x-rapidez::input.label>
    <x-rapidez::select name="something">
        <option value="1">Option 1</option>
    </x-rapidez::select>
</label>
```
--}}

@slots(['label'])

<label {{ $attributes->only(['class', 'v-bind:class', ':class'])->twMerge() }}>
    @if ($label->isNotEmpty())
        <x-rapidez::input.label>
            {{ $label }}
        </x-rapidez::input.label>
    @endif
    <x-rapidez::input.select.base :attributes="$attributes->except(['class', 'v-bind:class', ':class'])">
        {{ $slot }}
    </x-rapidez::input.select.base>
</label>