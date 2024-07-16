{{--
Examples:

1. Just a radio
```
<x-rapidez::input.radio name="something" class="border-red-500" />
```

2. With a label
```
<x-rapidez::input.radio name="something" class="border-red-500">
    Option 1
</x-rapidez::input.radio>
```

3. With a custom styled label
```
<x-rapidez::input.radio name="something" class="border-red-500">
    <span class="font-bold">
        Option 1
    </span>
</x-rapidez::input.radio>
```

4. Conditional with Vue
```
<template v-if="someCondition">
    <x-rapidez::input.radio name="something" class="border-red-500">
        <span class="font-bold">
            Option 1
        </span>
    </x-rapidez::input.radio>
</template>
```
--}}

<label {{ $attributes->only('class')->twMerge('inline-flex text-neutral text-sm cursor-pointer has-[:required]:after:content-[\'*\']') }}>
    <input type="radio" class="cursor-pointer border focus:ring-neutral size-5 mt-0.5 mr-2.5 text-neutral border-border focus:outline-none focus:ring-0 focus:ring-offset-0" {{ $attributes->except('class') }}>
    {{ $slot }}
</label>

