{{--
Examples:

1. Just a radio
```
<x-rapidez::input.radio.base name="something" class="border-red-500" />
```

2. Custom with a label
```
<label class="flex">
    <x-rapidez::input.radio.base name="something" class="mr-2.5" />
    <x-rapidez::input.label>@lang('Something')</x-rapidez::input.label>
</label>
```
--}}

<input type="radio" {{ $attributes->twMerge('cursor-pointer border focus:ring-neutral size-5 text-neutral border-border focus:outline-none focus:ring-0 focus:ring-offset-0') }} />