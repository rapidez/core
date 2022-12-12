<label class="flex items-center">
    <input type="checkbox" {{ $attributes->merge(['class' => 'focus:ring-primary h-4 w-4 text-primary border rounded']) }}>
    <div class="ml-2 text-gray-600">
        {{ $slot }}
    </div>
</label>
