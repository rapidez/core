{{--
Examples:

1. Just an input:
```
<x-rapidez::input name="something"/>
```

2. With a label:
```
<label>
    <x-rapidez::label>@lang('Something')</x-rapidez::label>
    <x-rapidez::input name="something"/>
</label>
```
--}}
<input {{ $attributes->twMerge('block w-full py-2 px-3 rounded border border-border focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 sm:text-sm') }}>
