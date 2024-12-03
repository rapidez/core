<label class="flex items-center">
    <input type="checkbox" {{ $attributes->merge(['class' => 'focus:ring-current h-4 w-4 text border rounded']) }}>
    <div class="ml-2 text">
        {{ $slot }}
    </div>
</label>
