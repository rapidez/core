@props(['color' => '\'none\''])

<label class="group/swatch cursor-pointer flex items-center justify-center p-1 rounded-full ring-inset relative ring-default ring-1 hover:ring-emphasis has-[:checked]:ring-active has-[:checked]:ring-2 has-[:checked]:hover:ring-active group">
    <span class="size-5 block border border-black/15 rounded-full m-px" v-bind:style="{ background: {{ $color }} }"></span>
    <input {{ $attributes->class('opacity-0 size-0 absolute') }}>
    @if ($slot->isNotEmpty())
        <span class="pointer-events-none absolute bottom-full mb-1 rounded px-1.5 py-0.5 bg-active text-white opacity-0 group-hover/swatch:opacity-100 transition-opacity">
            {{ $slot }}
        </span>
    @endif
</label>
