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
<label class="flex items-center justify-between gap-x-2 border-t pb-2.5 pt-4 text cursor-pointer peer-checked:[&>.chevron]:rotate-180" :for="id">
    <span class="block font-sans text-base font-semibold">
        @slotdefault('title')
            @{{ filter?.name?.replace('_', ' ') }}
        @endslotdefault
    </span>

    <x-heroicon-o-chevron-down class="size-4 chevron transition" />
</label>
<div class="peer-checked:*:-my-1 peer-checked:*:py-1 grid grid-rows-[0fr] transition-all peer-checked:grid-rows-[1fr]">
    {{-- TODO: Why do we need these classes? --}}
    <div class="-mx-1 overflow-hidden px-1">
        {{ $slot }}
    </div>
</div>
