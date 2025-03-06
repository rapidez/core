
<nav class="flex my-5">
    @foreach ($checkoutSteps as $checkoutStepKey => $checkoutStep)
        <div class="text-center w-full relative focus:outline-hidden">
            @if (!$loop->last)
                <div class="absolute flex w-full h-0.5 top-5 left-1/2 {{ $currentStepKey > $checkoutStepKey ? 'bg-emphasis' : 'bg' }}"></div>
            @endif
            <a href="{{ route('checkout', $checkoutStep) }}" @class([
                'relative flex size-10 mx-auto justify-center rounded-full font-bold items-center',
                'pointer-events-none cursor-default' => $currentStepKey < $checkoutStepKey,
                'bg-secondary' => $checkoutStepKey < $currentStepKey,
                'text-white' => $checkoutStepKey <= $currentStepKey,
                'bg-white border' => $checkoutStepKey > $currentStepKey,
                'bg-primary pointer-events-none cursor-default' => $checkoutStepKey === $currentStepKey
            ])>
                {{ $checkoutStepKey + 1 }}
            </a>
            <span class="hidden md:block">@lang(ucfirst($checkoutStep))</span>
        </div>
    @endforeach
</nav>
