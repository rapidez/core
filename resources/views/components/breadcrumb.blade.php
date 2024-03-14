@props(['url', 'position', 'active' => false])

<li class="flex items-center" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
    @if (!$active)
        <a {{ $attributes->twMerge('text-sm text-neutral hover:underline')->merge(['href' => url($url), 'itemprop' => 'item']) }}>
            <span itemprop="name">{{ $slot }}</span>
            <meta itemprop="position" content="{{ $position }}" />
        </a>
        <x-heroicon-s-chevron-right class="h-4 w-4 text-gray-400"/>
    @else
        <span class="text-sm" itemprop="name">{{ $slot }}</span>
        <meta itemprop="position" content="{{ $position }}" />
    @endif
</li>
