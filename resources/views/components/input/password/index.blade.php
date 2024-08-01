{{--
Examples:

1. Just a password:
```
<x-rapidez::input.password />
```

2. With a label:
```
<label>
    <x-rapidez::input.label>Password</x-rapidez::input.label>
    <x-rapidez::input.password />
</label>
```

3. Conditional with vue:
```
<template v-if="true">
    <label>
        <x-rapidez::input.label>Password</x-rapidez::input.label>
        <x-rapidez::input.password />
    </label>
</template>
```
--}}

<toggler>
    <span class="block relative" slot-scope="{ isOpen, toggle }">
        <x-rapidez::input
            name="password"
            type="password"
            placeholder="password"
            v-bind:type="isOpen ? 'text' : 'password'"
            class="pr-10"
            :$attributes
        />
        @if (!$attributes['disabled'] ?? false)
            <span v-on:click="toggle" class="absolute right-5 top-1/2 -translate-y-1/2 cursor-pointer">
                <x-heroicon-o-eye v-if="isOpen" class="h-4" v-cloak/>
                <x-heroicon-o-eye-slash v-else class="h-4"/>
            </span>
        @endif
    </span>
</toggler>