<label class="flex items-center">
    <input type="radio" {{ $attributes->merge(['class' => 'focus:ring-neutral h-4 w-4 text-neutral border']) }}>
    <div class="ml-2 text-neutral">
        {{ $slot }}
    </div>
</label>
