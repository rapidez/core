{{--
Examples:

1. Just a select without a label:
```
<x-rapidez::input.select.base>
    <option value="1">Option 1</option>
</x-rapidez::input.select.base>
```

2. With a label:
```
<x-rapidez::input.select" label="something">
    <option value="1">Option 1</option>
</x-rapidez::input.select>
```

3. With a custom styled label:
```
<x-rapidez::input.select">
    <x-slot:label>
        <span class="text-red-700">@lang('something')</span>
    </x-slot:label>
    <option value="1">Option 1</option>
</x-rapidez::input.select>
```
--}}
<select {{ $attributes->twMerge('w-full py-3 px-5 border rounded-md border-border outline-0 ring-0 text-sm transition-colors focus:ring-transparent focus:border-primary disabled:cursor-not-allowed disabled:bg-disabled disabled:border-disabled placeholder:text-inactive') }}>
    {{ $slot }}
</select>
