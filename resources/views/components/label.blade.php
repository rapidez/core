@slots(['label'])

<label {{ $attributes->twMerge('flex flex-col-reverse gap-y-2 text-sm relative') }}>
    {{ $slot }}
    <span {{ $label->attributes->twMerge('text-sm peer-required:after:content-[\'*\']') }}>
        {{ $label }}
    </span>
</label>
