@props(['id' => null, 'name' => null])

<label class="flex items-center">
    <input
        type="checkbox"
        {{ $attributes->twMerge('focus:ring-neutral h-4 w-4 text-neutral border rounded') }}
        id="{{ $id ?? $name }}"
        name="{{ $name ?? $id }}"
    >
    <div class="ml-2 text-gray-600">
        {{ $slot }}
    </div>
</label>
