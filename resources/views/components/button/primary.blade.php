{{--
This is the primary button.

Example:
```
    <x-rapidez:button.primary>Something</x-rapidez:button.primary>
```
--}}

<x-rapidez::button {{ $attributes->twMerge('bg-primary text-white border-b border-black/15') }}>
    {{ $slot }}
</x-rapidez::button>
