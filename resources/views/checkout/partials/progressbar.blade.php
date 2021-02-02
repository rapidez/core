<nav class="grid grid-cols-12 text-center my-5">
    @foreach(['Cart', 'Login', 'Credentials', 'Payment'] as $stepTitle)
        <button class="col-span-3 relative focus:outline-none" :disabled="checkout.step < {{ $loop->index }}" :class="checkout.step < {{ $loop->index }} ? 'cursor-default' : ''" v-on:click="if (checkout.step >= {{ $loop->index }}) goToStep({{ $loop->index }})">
            @if(!$loop->last)
                <div :class="checkout.step > {{ $loop->index }} ? 'bg-primary' : 'bg-secondary'" class="absolute flex w-full h-0.5 top-5 left-1/2"></div>
            @endif
            <div :class="checkout.step >= {{ $loop->index }} ? 'border-primary' : 'border-secondary'" class="relative flex w-10 h-10 mx-auto justify-center rounded-full font-bold bg-white items-center border">{{ $loop->index + 1 }}</div>
            <span class="hidden sm:block">@lang($stepTitle)<span>
        </button>
    @endforeach
</nav>
