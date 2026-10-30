<div v-if="items.length" class="flex flex-col gap-5">
    @if ($title)
        <strong class="font-bold text-2xl">
            @lang($title)
        </strong>
    @endif
    @slotdefault('items')
        <x-rapidez::slider />
    @endslotdefault
</div>
