{{--
Examples:

1. Just a checkbox
```
<x-rapidez::input.checkbox.base name="something" class="text-red-500" />
```

2. Custom with a label
```
<label class="flex">
    <x-rapidez::input.checkbox.base name="something" class="mr-2.5" />
    <x-rapidez::input.label>@lang('Something')</x-rapidez::input.label>
</label>
```
--}}

<input type="checkbox" {{ $attributes->twMerge('cursor-pointer border rounded-md focus:ring-neutral size-5 text-neutral border-border focus:outline-none focus:ring-0 focus:ring-offset-0') }}>
