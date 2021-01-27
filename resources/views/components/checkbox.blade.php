<label class="flex items-center">
    <input type="checkbox" {{ $attributes->merge(['class' => 'focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded']) }}>
    <div class="ml-2 text-gray-700">
        {{ $slot }}
    </div>
</label>
