{{--
Examples:

1. Just a radio (this is using the `input.radio` component!)
```
<x-rapidez::input.radio name="something" class="border-red-500" />
```

2. With a label
```
<x-rapidez::radio name="something" class="border-red-500">
    Option 1
</x-rapidez::radio>
```

3. With a custom styled label
```
<x-rapidez::radio name="something" class="border-red-500">
    <x-slot:slot class="font-bold">
        Option 1
    </x-slot:slot>
</x-rapidez::radio>
```

4. Conditional with Vue
```
<template v-if="someCondition">
    <x-rapidez::radio name="something" class="border-red-500">
        <x-slot:slot class="font-bold">
            Option 1
        </x-slot:slot>
    </x-rapidez::radio>
</template>
```
--}}
<label class="flex-inline items-center gap-2">
    <x-rapidez::input.radio :$attributes/>
    <span {{ $slot->attributes->twMerge('text-gray-600') }}>
        {{ $slot }}
    </span>
</label>
