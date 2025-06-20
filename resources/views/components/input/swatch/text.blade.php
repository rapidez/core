<label class="block border px-3 py-1.5 rounded-md cursor-pointer text-sm text-muted font-medium hover:border-emphasis has-[:focus]:border-emphasis relative has-[:checked]:bg-active has-[:checked]:!border-active has-[:checked]:text-white">
    {{ $slot }}
    <input {{ $attributes->class('opacity-0 size-0 absolute') }}>
</label>
