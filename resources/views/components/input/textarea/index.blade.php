{{--
Examples:

1. Just a textarea without a label:
```
<x-rapidez::input.textarea.base />
```

2. With a label:
```
<x-rapidez::input.textarea label="something" />
```

3. With a custom styled label:
```
<x-rapidez::input.textarea">
    <x-slot:label>
        <span class="text-red-700">@lang('something')</span>
    </x-slot:label>
</x-rapidez::input.textarea>
```
--}}

@slots(['label'])

<label {{ $attributes->only(['class', 'v-bind:class', ':class'])->twMerge() }}>
    @if ($label->isNotEmpty())
        <x-rapidez::input.label>
            {{ $label }}
        </x-rapidez::input.label>
    @endif
    <x-rapidez::input.textarea.base :attributes="$attributes->except(['class', 'v-bind:class', ':class'])" />
    {{ $slot }}
</label>
