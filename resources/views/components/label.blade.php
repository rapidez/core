@slots(['label'])

<label {{ $attributes->class('flex flex-col-reverse gap-y-2 text-sm relative') }}>
    {{ $slot }}
    <span {{ $label->attributes->merge(['class' => "text-sm peer-required:after:content-['*']"]) }}>
        {{ $label }}
    </span>
</label>
