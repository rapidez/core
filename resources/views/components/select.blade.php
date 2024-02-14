@props(['id' => null, 'name' => null])

<select
    {{ $attributes->twMerge('w-full peer rounded border border-border bg-white py-4 px-5 text-sm outline-none !ring-0 transition-all placeholder:text-inactive disabled:bg-gray-50 disabled:cursor-not-allowed font-medium') }}
    id="{{ $id ?? $name }}"
    name="{{ $name ?? $id }}"
>
    {{ $slot }}
</select>
