{{--
Examples:

1. Just a select without a label:
```
<x-rapidez::input.select.base>
    <option value="1">Option 1</option>
</x-rapidez::input.select.base>
```

2. With a label:
```
<x-rapidez::input.select" label="something">
    <option value="1">Option 1</option>
</x-rapidez::input.select>
```

3. With a custom styled label:
```
<x-rapidez::input.select">
    <x-slot:label>
        <span class="text-red-700">@lang('something')</span>
    </x-slot:label>
    <option value="1">Option 1</option>
</x-rapidez::input.select>
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