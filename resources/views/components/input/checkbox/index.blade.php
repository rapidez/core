{{--
Examples:

1. Checkbox with a label:
```
<x-rapidez::input.checkbox>
    This is the label
</x-rapidez::input.checkbox>
```

2. Conditional with Vue:
```
<template v-if="true">
    <x-rapidez::input.checkbox>
        This is the label
    </x-rapidez::input.checkbox>
</template>
```

3. Just a simple checkbox:
```
<x-rapidez::input.checkbox.base />
```
--}}

<label class="inline-flex text-neutral text-sm cursor-pointer has-[:required]:after:content-['*']">
    <x-rapidez::input.checkbox.base class="mr-2.5" :$attributes />
    {{ $slot }}
</label>