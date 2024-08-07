{{--
This button is used for sliders.

Example:
```
    <x-rapidez:button.slider>Icon</x-rapidez:button.slider>
```
--}}

<x-rapidez::button {{ $attributes->twMerge('inline-flex bg-white p-0 rounded-full size-14 items-center justify-center shadow bg-white text-neutral border') }}>
    {{ $slot }}
</x-rapidez::button>
