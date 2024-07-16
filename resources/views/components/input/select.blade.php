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
    <x-rapidez::input.label>@lang('Something')</x-rapidez::input.label>
    <x-rapidez::select name="something">
        <option value="1">Option 1</option>
    </x-rapidez::select>
</label>
```
--}}
<select {{ $attributes->twMerge('w-full py-3 px-5 border rounded-md border-border outline-0 ring-0 text-sm transition-colors focus:ring-transparent focus:border-primary disabled:cursor-not-allowed disabled:bg-disabled disabled:border-disabled placeholder:text-inactive') }}>
    {{ $slot }}
</select>
