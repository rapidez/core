{{-- TODO: When the steps count change this grids doesn't play nice --}}
<nav class="grid grid-cols-12 my-5">
    @foreach ($checkoutSteps as $checkoutStepKey => $checkoutStep)
        <a href="{{ route('checkout', $checkoutStep) }}" @class([
            'text-center col-span-3 relative focus:outline-none',
            'pointer-events-none cursor-default' => $currentStepKey < $checkoutStepKey,
        ])>
            @if (!$loop->last)
                {{-- TODO: This line is clickable which it shouldn't --}}
                <div class="absolute flex w-full h-0.5 top-5 left-1/2 {{ $currentStepKey > $checkoutStepKey ? 'bg-neutral' : 'bg-inactive' }}"></div>
            @endif
            <div @class([
                'relative flex w-10 h-10 mx-auto justify-center rounded-full font-bold items-center border border-neutral',
                'bg-neutral text-white' => $checkoutStepKey <= $currentStepKey,
                'bg-white' => $checkoutStepKey > $currentStepKey,
                'bg-neutral text-white shadow-md shadow-neutral' => $checkoutStepKey === $currentStepKey
            ])>
                {{ $checkoutStepKey + 1 }}
            </div>
            <span class="hidden sm:block">@lang(ucfirst($checkoutStep))<span>
        </a>
    @endforeach
</nav>
