@props(['heading'])

<input class="peer hidden" type="checkbox" checked :set="id = Math.random().toString(36).slice(2)" :id="id" />

<label class="flex cursor-pointer items-center justify-between gap-x-2 border-t pb-2.5 pt-4 text-neutral peer-checked:[&>.chevron]:rotate-180" :for="id">
    <span class="block font-sans text-base font-semibold text-neutral">
        @isset($heading)
            {{ $heading }}
        @else
            @{{ filter?.name?.replace('_', ' ') }}
        @endisset
    </span>

    <x-heroicon-o-chevron-down class="size-4 chevron transition" />
</label>
<div class="grid grid-rows-[0fr] transition-all peer-checked:grid-rows-[1fr] peer-checked:*:-my-1 peer-checked:*:py-1">
    <div class="overflow-hidden px-1 -mx-1">
        {{ $slot }}
    </div>
</div>
