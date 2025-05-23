@slots(['summary', 'content', 'icon'])

<details {{ $attributes->twMerge('group/details details-content:h-0 details-content:overflow-clip details-content:transition-[height,content-visibility] details-content:transition-discrete details-content:duration-200 open:details-content:h-auto') }}>
    <summary {{ $summary->attributes->twMerge('flex items-center font-medium py-3.5 cursor-pointer list-none') }}>
        {{ $summary }}

        @if ($icon)
            @slotdefault('icon')
                <x-heroicon-o-chevron-down class="ml-auto size-4 flex shrink-0 text-muted group-open/details:rotate-180 transition" stroke-width="2" />
            @endslotdefault
        @endif
    </summary>

    <div {{ $content->attributes->twMerge('pb-5') }}>
        {{ $content }}
    </div>
</details>
