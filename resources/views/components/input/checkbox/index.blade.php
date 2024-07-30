{{--
Examples:

1. Checkbox with a label
```
<x-rapidez::input.checkbox.base name="something" class="border-red-500">
    Accept!
</x-rapidez::input.checkbox.base>
```

2. With a custom styled label
```
<x-rapidez::input.checkbox name="something" class="border-red-500">
    <span class="font-bold">
        Accept!
    </span>
</x-rapidez::input.checkbox>
```

3. Conditional with Vue
```
<template v-if="someCondition">
    <x-rapidez::input.checkbox name="something" class="border-red-500">
        <span class="font-bold">
            Accept!
        </span>
    </x-rapidez::input.checkbox>
</template>
```
--}}

<label {{ $attributes->only(['class', 'v-bind:class', ':class'])->twMerge('inline-flex text-neutral text-sm cursor-pointer has-[:required]:after:content-[\'*\']') }}>
    <x-rapidez::input.checkbox.base class="mr-2.5" :attributes="$attributes->except(['class', 'v-bind:class', ':class'])" />
    {{ $slot }}
</label>