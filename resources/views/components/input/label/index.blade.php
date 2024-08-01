{{--
It's called a label but it isn't? 🤯 Yup, as it's just for styling 🎨
The benefit of this approach is that you don't have to deal with
"for" attributes on labels and matching input names. Usage:
```
<label>
    <x-rapidez::input.label>Label</x-rapidez::input.label>
    <x-rapidez::input />
</label>
```
By default a label has an asterix when the input is required. If you don't want an asterix
you can use the label like this:
```
<x-rapidez::input.label class="after:hidden">Label</x-rapidez::input.label>
```
--}}
<span {{ $attributes->twMerge("font-medium text-inactive text-sm block mb-2 has-[~_*_:required,~:required]:after:content-['*']") }}>
    {{ $slot }}
</span>