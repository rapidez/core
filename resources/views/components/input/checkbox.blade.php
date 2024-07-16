{{--
Examples:

1. Just a checkbox
```
<x-rapidez::input.checkbox name="something" class="border-red-500" />
```

2. With a label
```
<x-rapidez::input.checkbox name="something" class="border-red-500">
    Accept!
</x-rapidez::input.checkbox>
```

3. With a custom styled label
```
<x-rapidez::input.checkbox name="something" class="border-red-500">
    <span class="font-bold">
        Accept!
    </span>
</x-rapidez::input.checkbox>
```

4. Conditional with Vue
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

<label {{ $attributes->only('class')->twMerge('inline-flex text-neutral text-sm cursor-pointer has-[:required]:after:content-[\'*\']') }}>
    <input type="checkbox" class="cursor-pointer border rounded-md focus:ring-neutral size-5 mr-2.5 text-neutral border-border focus:outline-none focus:ring-0 focus:ring-offset-0" {{ $attributes->except('class') }}>
    {{ $slot }}
</label>
