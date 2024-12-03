<label class="flex items-center">
    <input type="radio" {{ $attributes->merge(['class' => 'focus:ring-border-default size-4 text border']) }}>
    <div class="ml-2 text">
        {{ $slot }}
    </div>
</label>
