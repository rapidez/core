@props(['url', 'position', 'active' => false])

<li class="flex items-center" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
    @if (!$active)
        <a {{ $attributes->merge(['class' => 'text-sm hover:underline', 'href' => url($url), 'itemprop' => 'item']) }}>
            <span itemprop="name">{{ $slot }}</span>
            <meta itemprop="position" content="{{ $position }}" />
        </a>
        <x-heroicon-o-chevron-right class="size-3 text-muted translate-y-px mx-1" stroke-width="2" />
    @else
        <span class="text-sm" itemprop="name">{{ $slot }}</span>
        <meta itemprop="position" content="{{ $position }}" />
    @endif
</li>
