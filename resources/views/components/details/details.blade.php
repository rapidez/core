@slots(['summary', 'content', 'icon'])

<details {{ $attributes->twMerge('group/details transition-details') }}>
    <summary {{ $summary->attributes->twMerge('flex items-center font-medium py-3.5 transition-all duration-300 cursor-pointer list-none') }}>
        {{ $summary }}

        @slotdefault('icon')
            <x-heroicon-o-chevron-down class="ml-auto size-4 flex shrink-0 text-muted group-open/details:rotate-180 transition" stroke-width="2" />
        @endslotdefault
    </summary>

    <div {{ $content->attributes->twMerge('pb-5') }}>
        {{ $content }}
    </div>
</details>
