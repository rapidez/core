@props(['tag' => 'div'])
<x-tag :is="$tag" {{ $attributes }}>
    {!! $slot !!}
</x-tag>
