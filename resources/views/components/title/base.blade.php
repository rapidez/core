@props(['tag' => 'div'])
<x-tag :is="$tag" {{ $attributes->class('text-pretty') }}>
    {!! $slot !!}
</x-tag>
