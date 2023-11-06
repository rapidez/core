<nav class="grid grid-cols-12 my-5">
    @foreach(array_slice((config('rapidez.frontend.checkout_steps.'.config('rapidez.store_code')) ?? config('rapidez.checkout_steps.default')), 0, -1) as $stepTitle)
        <button class="col-span-3 relative focus:outline-none" :disabled="checkout.step < {{ $loop->index }}" :class="checkout.step < {{ $loop->index }} ? 'cursor-default' : ''" v-on:click="if (checkout.step >= {{ $loop->index }}) goToStep({{ $loop->index }})">
            @if(!$loop->last)
                <div :class="checkout.step > {{ $loop->index }} ? 'bg-neutral' : 'bg-inactive'" class="absolute flex w-full h-0.5 top-5 left-1/2"></div>
            @endif
            <div
                :class="{'bg-neutral text-white': {{ $loop->index }} <= checkout.step, 'bg-white': {{ $loop->index }} > checkout.step, 'bg-neutral text-white shadow-md shadow-neutral': {{ $loop->index }} === checkout.step}"
                class="relative flex w-10 h-10 mx-auto justify-center rounded-full font-bold items-center border border-neutral"
            >
                {{ $loop->index + 1 }}
            </div>
            <span class="hidden sm:block">@lang(ucfirst($stepTitle))<span>
        </button>
    @endforeach
</nav>
