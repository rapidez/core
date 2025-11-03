{{-- This title is shown in the search autocomplete above the products and categories --}}
<div {{ $attributes->twMerge('text-muted text-xs pb-1 sm:px-5') }}>
    {{ $slot }}
</div>
