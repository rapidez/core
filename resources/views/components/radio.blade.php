@props(['id' => null, 'name' => null])

<input
    type="radio"
    {{ $attributes->twMerge('focus:ring-neutral h-4 w-4 text-neutral border mr-2') }}
    id="{{ $id ?? $name }}"
    name="{{ $name ?? $id }}"
>
