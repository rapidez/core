<label class="flex items-center">
    <input type="radio" {{ $attributes->merge(['class' => 'focus:ring-primary h-4 w-4 text-primary border']) }}>
    <div class="ml-2 text-gray-700">
        {{ $slot }}
    </div>
</label>
