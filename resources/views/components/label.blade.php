{{--
It's called a label but it isn't? 🤯 Yup, as it's just for styling 🎨
The benefit of this approach is that you don't have to deal with
"for" attributes on labels and matching input names. Usage:
```
<label>
    <x-rapidez::label>@lang('Firstname')</x-rapidez::label>
    <x-rapidez::input name="firstname"/>
</label>
```
--}}
<span {{ $attributes->twMerge('font-semibold text-inactive text-sm inline-block mb-2') }}>
    {{ $slot }}
</span>
