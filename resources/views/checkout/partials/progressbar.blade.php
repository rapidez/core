
<nav class="flex my-5">
    @foreach ($checkoutSteps as $checkoutStepKey => $checkoutStep)
        <div class="text-center w-full relative focus:outline-none">
            @if (!$loop->last)
                <div class="absolute flex w-full h-0.5 top-5 left-1/2 {{ $currentStepKey > $checkoutStepKey ? 'bg-neutral' : 'bg-inactive' }}"></div>
            @endif
            <a href="{{ route('checkout', $checkoutStep) }}" @class([
                'relative flex w-10 h-10 mx-auto justify-center rounded-full font-bold items-center border border-neutral',
                'pointer-events-none cursor-default' => $currentStepKey < $checkoutStepKey,
                'bg-neutral text-white' => $checkoutStepKey <= $currentStepKey,
                'bg-white' => $checkoutStepKey > $currentStepKey,
                'bg-neutral text-white shadow-neutral pointer-events-none cursor-default' => $checkoutStepKey === $currentStepKey
            ])>
                {{ $checkoutStepKey + 1 }}
            </a>
            <span class="hidden md:block">@lang(ucfirst($checkoutStep))</span>
        </div>
    @endforeach
</nav>
