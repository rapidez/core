{{--
Examples:

1. Just a simple checkbox:
```
<x-rapidez::input.checkbox.base />
```
--}}

<input type="checkbox" {{ $attributes->twMerge('cursor-pointer border rounded-md size-5 text-neutral border-border focus:outline-none focus:ring-0 focus:ring-offset-0') }} />
