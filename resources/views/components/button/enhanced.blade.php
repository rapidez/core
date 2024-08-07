{{--
This is the enhanced button the button will be used for the add to cart buttons
and it is used in the checkout.

Example:
```
    <x-rapidez:button.enhanced>Something</x-rapidez:button.enhanced>
```
--}}

<x-rapidez::button {{ $attributes->twMerge('bg-enhanced text-white border-b border-black/15') }}>
    {{ $slot }}
</x-rapidez::button>