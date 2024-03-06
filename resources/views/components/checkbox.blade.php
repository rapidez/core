@props(['id' => null, 'name' => null])

<input
    type="checkbox"
    {{ $attributes->twMerge('focus:ring-neutral h-4 w-4 text-neutral border rounded mr-2') }}
    id="{{ $id ?? $name }}"
    name="{{ $name ?? $id }}"
>
