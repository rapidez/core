@props(['score', 'count'])
<div class="flex items-center gap-1">
    <div class="relative">
        <div class="flex gap-0.5">
            @for ($star = 0; $star < 5; $star++)
                <x-heroicon-s-star class="size-5 text-muted/70"/>
            @endfor
        </div>
        <div class="flex gap-0.5 absolute inset-0 overflow-hidden" style="width: {{ $score }}%">
            @for ($star = 0; $star < 5; $star++)
                <x-heroicon-s-star class="size-5 shrink-0"/>
            @endfor
        </div>
    </div>
    <span class="text-sm">({{ $count }})</span>
</div>
