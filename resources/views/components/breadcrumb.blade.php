@props(['url', 'active' => false])

@if(!$active)
    <a {{ $attributes->merge(['class' => 'text-sm text-primary hover:underline', 'href' => $url]) }}>
        {{ $slot }}
    </a>
    <x-heroicon-s-chevron-right class="h-4 w-4 text-gray-400"/>
@else
    <span class="text-sm">{{ $slot }}</span>
@endif
