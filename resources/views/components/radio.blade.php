<label class="flex items-center">
    <input type="radio" {{ $attributes->merge(['class' => 'focus:ring-neutral text-neutral size-4 border border-neutral-110']) }}>
    <div class="ml-2">
        {{ $slot }}
    </div>
</label>
