@slots(['title'])

{{--
TODO: Can't we use <details> with <summary> for this?
Or maybe is JS easier so we don't need an input?
--}}
<input class="peer hidden" type="checkbox" checked :set="id = Math.random().toString(36).slice(2)" :id="id" />

{{--
TODO: Double check these paddings;
why are they different? And why
do we've pb-4 wrapped around
it from the filters?
--}}
<label class="flex items-center justify-between gap-x-2 border-t pt-4 text cursor-pointer peer-checked:[&>svg]:rotate-180" :for="id">
    <span class="block font-sans font-medium">
        @slotdefault('title')
            @{{ filter?.name?.replace('_', ' ') }}
        @endslotdefault
    </span>

    <x-heroicon-o-chevron-down class="size-4 transition text-muted" stroke-width="2" />
</label>
<div class="grid grid-rows-[0fr] transition-[grid-template-rows] peer-checked:grid-rows-[1fr]">
    {{-- TODO: Why do we need these classes? --}}
    <div class="overflow-hidden px-2 pb-2 -mx-2 -mb-2">
        <div class="pt-3">
            {{ $slot }}
        </div>
    </div>
</div>
