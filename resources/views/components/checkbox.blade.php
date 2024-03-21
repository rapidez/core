{{--
Examples:

1. Just a checkbox (this is using the `input.checkbox` component!)
```
<x-rapidez::input.checkbox name="something" class="border-red-500" />
```

2. With a label
```
<x-rapidez::checkbox name="something" class="border-red-500">
    Accept!
</x-rapidez::checkbox>
```

3. With a custom styled label
```
<x-rapidez::checkbox name="something" class="border-red-500">
    <x-slot:slot class="font-bold">
        Accept!
    </x-slot:slot>
</x-rapidez::checkbox>
```

4. Conditional with Vue
```
<template v-if="someCondition">
    <x-rapidez::checkbox name="something" class="border-red-500">
        <x-slot:slot class="font-bold">
            Accept!
        </x-slot:slot>
    </x-rapidez::checkbox>
</template>
```
--}}
<label class="flex-inline items-center gap-2">
    <x-rapidez::input.checkbox :$attributes/>
    <span {{ $slot->attributes->twMerge('text-gray-600') }}>
        {{ $slot }}
    </span>
</label>
