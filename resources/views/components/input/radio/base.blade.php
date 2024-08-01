{{--
Examples:

1. Just a simple radio:
```
<x-rapidez::input.radio.base />
```
--}}

<input type="radio" {{ $attributes->twMerge('cursor-pointer border size-5 text-neutral border-border focus:outline-none focus:ring-0 focus:ring-offset-0') }} />