<label class="flex items-center">
    <input type="radio" {{ $attributes->merge(['class' => 'focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300']) }}>
    <div class="ml-2 text-gray-800">
        {{ $slot }}
    </div>
</label>
