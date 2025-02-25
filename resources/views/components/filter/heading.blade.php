@slots(['title'])

<input class="peer hidden" type="checkbox" checked :set="id = Math.random().toString(36).slice(2)" :id="id" />

<label class="flex items-center justify-between gap-x-2 border-t pb-2.5 pt-4 text cursor-pointer [&>.chevron]:peer-checked:rotate-180" :for="id">
    <span class="block font-sans text-base font-semibold">
        @if ($title->isNotEmpty())
            {{ $title }}
        @else
            @{{ filter?.name?.replace('_', ' ') }}
        @endif
    </span>

    <x-heroicon-o-chevron-down class="size-4 chevron transition" />
</label>
<div class="*:peer-checked:-my-1 *:peer-checked:py-1 grid grid-rows-[0fr] transition-all peer-checked:grid-rows-[1fr]">
    <div class="-mx-1 overflow-hidden px-1">
        {{ $slot }}
    </div>
</div>
