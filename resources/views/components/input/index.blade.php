{{--
Examples:

1. Just an input without a label:
```
<x-rapidez::input.base name="something"/>
```

2. With a label:
```
<x-rapidez::input label="something"/>
```

3. With a label as a slot:
```
<x-rapidez::input">
    <x-slot:label>
        @lang('something')
    </x-slot:label>
</x-rapidez::input>
```
--}}
@slots(['label'])

<label {{ $attributes->only(['class', 'v-bind:class', ':class'])->twMerge() }}>
    @if ($label->isNotEmpty())
        <x-rapidez::input.label>
            {{ $label }}
        </x-rapidez::input.label>
    @endif
    <x-rapidez::input.base :attributes="$attributes->except(['class', 'v-bind:class', ':class'])"/>
    {{ $slot }}
</label>