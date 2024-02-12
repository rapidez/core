@props(['label', 'srOnlyLabel'])
@slots(['label'])

<label {{ $attributes->twMerge('flex flex-col-reverse gap-y-2 text-sm relative') }}>
    {{ $slot }}
    @if (($label ?? '') && $label->isNotEmpty())
        <span {{ $label->attributes->twMerge('text-sm peer-required:after:content-[\'*\']' . (($srOnlyLabel ?? false) ? ' sr-only' : '')) }}>
            {{ $label }}
        </span>
    @endif
</label>
