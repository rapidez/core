@props(['mobileOnly' => false])

<toggler v-slot="{ isOpen, toggle, close }">
    <div>
        {{ $button }}
        <div
            @class([
                'fixed inset-0 overflow-hidden',
                'md:contents' => $mobileOnly,
                config('rapidez.z-indexes.slideover'),
            ])
            @if($mobileOnly)
                :class="isOpen ? 'max-md:pointer-events-auto' : 'max-md:pointer-events-none'"
            @else
                :class="isOpen ? 'pointer-events-auto' : 'pointer-events-none'"
            @endif
        >
            <div
                @class([
                    'pointer-events-none absolute inset-0 -z-10 cursor-pointer bg-neutral opacity-0 transition-opacity',
                    'md:hidden' => $mobileOnly,
                ])
                :class="{ '!opacity-75 pointer-events-auto': isOpen }"
                v-on:click="toggle"
            ></div>
            <div
                @class([
                    'absolute inset-y-0 flex w-full max-w-md flex-col bg-white px-5 py-6 transition-[right]',
                    'md:contents' => $mobileOnly,
                ])
                :class="isOpen ? 'right-0' : '-right-full'"
            >
                <div @class([
                    'flex items-center justify-between pb-6',
                    'md:hidden' => $mobileOnly,
                ])>
                    <h2 class="text-lg font-medium">
                        {{ $title }}
                    </h2>
                    <button
                        class="text-gray-400 transition hover:text-text-neutral"
                        aria-label="@lang('Close filters')"
                        v-on:click="toggle"
                    >
                        <x-heroicon-o-x-mark class="h-6 w-6" />
                    </button>
                </div>
                <div @class([
                    'max-h-full overflow-y-auto scrollbar-hide',
                    'md:contents' => $mobileOnly,
                ])>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</toggler>
