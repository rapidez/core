{{--
Examples:

1. Radio with a label
```
<x-rapidez::input.radio.base name="something" class="text-red-500">
    Option 1
</x-rapidez::input.radio.base>
```

2. With a custom styled label
```
<x-rapidez::input.radio name="something" class="text-red-500">
    <span class="font-bold">
        Option 1
    </span>
</x-rapidez::input.radio>
```

3. Conditional with Vue
```
<template v-if="someCondition">
    <x-rapidez::input.radio name="something" class="text-red-500">
        <span class="font-bold">
            Option 1
        </span>
    </x-rapidez::input.radio>
</template>
```
--}}

<label {{ $attributes->only(['class', 'v-bind:class', ':class'])->twMerge('inline-flex text-neutral text-sm cursor-pointer has-[:required]:after:content-[\'*\']') }}>
    <x-rapidez::input.radio.base class="mr-2.5" :attributes="$attributes->except(['class', 'v-bind:class', ':class'])" />
    {{ $slot }}
</label>