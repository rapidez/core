{{--
Examples:

1. Just an input:
```
<x-rapidez::input name="something"/>
```

2. With a label:
```
<label>
    <x-rapidez::input.label>@lang('Something')</x-rapidez::input.label>
    <x-rapidez::input name="something"/>
</label>
```
--}}
<input {{ $attributes->twMerge('w-full py-3 px-5 border rounded-md border-border outline-0 ring-0 text-sm transition-colors focus:ring-transparent focus:border-primary disabled:cursor-not-allowed disabled:bg-disabled disabled:border-disabled placeholder:text-inactive [&::-webkit-inner-spin-button]:appearance-none [&:user-invalid:not(:placeholder-shown)]:border-red-500') }}>
