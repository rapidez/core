{{--
Examples:

1. Just an input:
```
<x-rapidez::input.password />
```

2. With a label:
```
<label>
    <x-rapidez::input.label>@lang('Password')</x-rapidez::input.label>
    <x-rapidez::input.password />
</label>
```
--}}

<toggler>
    <span {{ $attributes->only('class')->twMerge('block relative') }} slot-scope="{ isOpen, toggle }">
        <x-rapidez::input.base
            name="password"
            type="password"
            placeholder="password"
            v-bind:type="isOpen ? 'text' : 'password'"
            class="pr-10"
            {{ $attributes->except('class') }}
        />
        @if (!$attributes['disabled'] ?? false)
            <span v-on:click="toggle" class="absolute right-5 top-1/2 -translate-y-1/2 cursor-pointer">
                <x-heroicon-o-eye v-if="isOpen" class="h-4" v-cloak/>
                <x-heroicon-o-eye-slash v-else class="h-4"/>
            </span>
        @endif
    </span>
</toggler>