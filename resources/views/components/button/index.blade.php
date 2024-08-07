{{--
This is the default button if you use the example you get this.

Example:
```
    <x-rapidez:button>Something</x-rapidez:button>
```
--}}

<x-rapidez::button.base {{ $attributes->twMerge('relative inline-flex items-center justify-center transition text-white bg-neutral text-base font-medium rounded min-h-12 py-1.5 px-5 hover:bg-opacity-80 border-b border-black/15 disabled:text-inactive disabled:bg-disabled disabled:cursor-not-allowed') }}>
    {{ $slot }}
</x-rapidez::button.base>
