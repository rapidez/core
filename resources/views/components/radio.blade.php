@props(['id' => null, 'name' => null])

<label class="flex items-center">
    <input
        type="radio"
        {{ $attributes->twMerge('focus:ring-neutral h-4 w-4 text-neutral border') }}
        id="{{ $id ?? $name }}"
        name="{{ $name ?? $id }}"
    >
    <div class="ml-2 text-gray-700">
        {{ $slot }}
    </div>
</label>
