{{-- This title is shown in the search autocomplete above the products and categories --}}
<div {{ $attributes->twMerge('text-muted text-xs font-semibold pb-2 px-5') }}>
    {{ $slot }}
</div>
