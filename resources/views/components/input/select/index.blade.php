{{--
Examples:

1. Just a select:
```
<x-rapidez::input.select>
    <option>Option 1</option>
</x-rapidez::input.select>
```

2. With a label:
```
<label>
    <x-rapidez::label>Something</x-rapidez::label>
    <x-rapidez::input.select>
        <option>Option 1</option>
    </x-rapidez::input.select>
</label>
```
--}}

<select {{ $attributes->twMerge('w-full py-3 px-5 border rounded-md border-border outline-0 ring-0 text-sm transition-colors focus:ring-transparent focus:border-neutral disabled:cursor-not-allowed disabled:bg-disabled disabled:border-disabled placeholder:text-inactive') }}>
    {{ $slot }}
</select>
