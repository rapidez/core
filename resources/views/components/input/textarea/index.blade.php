{{--
Examples:

1. Just a textarea:
```
<x-rapidez::input.textarea />
```

2 With a label:
```
<label>
    <x-rapidez::label>Something</x-rapidez::label>
    <x-rapidez::input.textarea />
</label>
```
--}}

<textarea {{ $attributes->twMerge('w-full py-3 px-5 border rounded-md border-border outline-0 ring-0 text-sm transition-colors focus:ring-transparent focus:border-neutral disabled:cursor-not-allowed disabled:bg-disabled disabled:border-disabled placeholder:text-inactive') }}></textarea>
