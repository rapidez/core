{{--
Examples:

1. Just a select:
```
<x-rapidez::select name="something">
    <option value="1">Option 1</option>
</x-rapidez::select>
```

2. With a label:
```
<label>
    <x-rapidez::label>@lang('Something')</x-rapidez::label>
    <x-rapidez::select name="something">
        <option value="1">Option 1</option>
    </x-rapidez::select>
</label>
```
--}}
<select {{ $attributes->twMerge('block w-full py-2 pl-3 pr-8 rounded border border-border focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 sm:text-sm') }}>
    {{ $slot }}
</select>
