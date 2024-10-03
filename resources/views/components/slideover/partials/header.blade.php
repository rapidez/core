<div class="slideover-header bg-primary py-5">
    <div class="px-5">
        <div class="relative flex items-center justify-center">
            @if ($hasParent)
                <label
                    @if($idBind)
                        v-bind:for="{{ $idBind }}"
                    @else
                        for="{{ $id }}"
                    @endif
                    class="absolute left-0 top-1/2 -translate-y-1/2 cursor-pointer text-white">
                    <x-heroicon-o-arrow-left class="size-6" />
                </label>
            @elseif ($headerbutton->isNotEmpty())
                {{ $headerbutton }}
            @endif
            @if ($title)
                <span {{ $title->attributes->class('text-base max-w-full px-10 truncate font-semibold text-white antialiased') }}>
                    {{ $title }}
                </span>
            @endif
            <label
                @if($idBind)
                    v-bind:for="{{ $isInForm ? $closeId : $idBind }}"
                @else
                    for="{{ $isInForm ? $closeId : $id }}"
                @endif
                class="absolute right-0 top-1/2 -translate-y-1/2 cursor-pointer text-white">
                <x-heroicon-o-x-mark class="size-6" />
            </label>
        </div>
    </div>
</div>
