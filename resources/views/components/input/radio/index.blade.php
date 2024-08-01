{{--
Examples:

1. Radio with a label:
```
<x-rapidez::input.radio>
    This is the label
</x-rapidez::input.radio>
```

2. Conditional with Vue:
```
<template v-if="true">
    <x-rapidez::input.radio>
        This is the label
    </x-rapidez::input.radio>
</template>
```

3. Just a simple radio:
```
<x-rapidez::input.radio.base />
```
--}}

<label class="inline-flex text-neutral text-sm cursor-pointer has-[:required]:after:content-['*']">
    <x-rapidez::input.radio.base class="mr-2.5" :$attributes />
    {{ $slot }}
</label>