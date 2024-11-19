<label class="flex items-center">
    <input type="checkbox" {{ $attributes->merge(['class' => 'focus:ring-neutral size-4 text-neutral border border-neutral-110 rounded']) }}>
    <div class="ml-2 text-neutral">
        {{ $slot }}
    </div>
</label>
