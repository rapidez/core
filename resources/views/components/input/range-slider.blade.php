@props(['inputs' => true, 'labels' => false])
@slots(['value'])

<range-slider v-slot="rangeInput" {{ $attributes }}>
    <div class="w-full">
        <div @class([
                'flex flex-1 relative group/range-slider',
                'h-12 mx-1' => !$inputs
            ])
        >
            <input
                type="range"
                v-bind:disabled="!canRefine"
                v-bind:min="rangeInput.range.min"
                v-bind:max="rangeInput.range.max"
                v-model="rangeInput.minValue"
                v-on:change="rangeInput.updateRefinement"
                class="range-min absolute pointer-events-none appearance-none z-20 h-5 w-full opacity-0 cursor-pointer [&::-moz-range-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:pointer-events-auto"
                aria-label="@lang('Price from')"
            >

            <input
                type="range"
                v-bind:disabled="!canRefine"
                v-bind:min="rangeInput.range.min"
                v-bind:max="rangeInput.range.max"
                v-model="rangeInput.maxValue"
                v-on:change="rangeInput.updateRefinement"
                class="range-max absolute pointer-events-none appearance-none z-20 h-5 w-full opacity-0 cursor-pointer [&::-moz-range-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:pointer-events-auto"
                aria-label="@lang('Price to')"
            >

            <div class="relative z-10 w-full mx-2 h-4">
                <div class="absolute top-1/2 -translate-y-1/2 h-1 z-10 -inset-x-2 rounded-md bg-emphasis"></div>
                <div class="absolute top-1/2 -translate-y-1/2 h-1 z-20 rounded-md bg-active" v-bind:style="'right:'+rangeInput.maxThumb+'%; left:'+rangeInput.minThumb+'%'"></div>
                <div class="absolute z-30 size-4 top-1/2 left-0 bg-active rounded-full -translate-y-1/2 -translate-x-1/2 outline outline-transparent outline-1 outline-offset-1 group-has-[.range-min:focus]/range-slider:outline-emphasis" v-bind:style="'left: '+rangeInput.minThumb+'%'"></div>
                <div class="absolute z-30 size-4 top-1/2 right-0 bg-active rounded-full -translate-y-1/2 translate-x-1/2 outline outline-transparent outline-1 outline-offset-1 group-has-[.range-max:focus]/range-slider:outline-emphasis" v-bind:style="'right: '+rangeInput.maxThumb+'%'"></div>
                @if ($labels)
                    <div
                        class="absolute z-30 top-5 left-0 bg-active text-white px-2 py-0.5 rounded-md -translate-x-1/2 whitespace-nowrap"
                        v-bind:style="'left: '+rangeInputScope.minThumb+'%;--tw-translate-x:-'+rangeInputScope.minThumb+'%'"
                        v-bind:set="rangeInput.value = rangeInputScope.minValue"
                    >
                        @if ($value->isNotEmpty())
                            {{ $value }}
                        @else
                            @{{ rangeInput.prefix }} <span v-text="rangeInput.value"></span> @{{ rangeInput.suffix }}
                        @endif
                    </div>
                    <div
                        class="absolute z-30 top-5 right-0 bg-active text-white px-2 py-0.5 rounded-md translate-x-1/2 whitespace-nowrap"
                        v-bind:style="'right: '+rangeInputScope.maxThumb+'%;--tw-translate-x:'+rangeInputScope.maxThumb+'%'"
                        v-bind:set="rangeInput.value = rangeInput.maxValue"
                    >
                        @if ($value->isNotEmpty())
                            {{ $value }}
                        @else
                            @{{ rangeInput.prefix }} <span v-text="rangeInput.value"></span> @{{ rangeInput.suffix }}
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @if ($inputs)
            <div @class([
                'flex items-center gap-x-4',
                'mt-4' => !$labels,
                'mt-9' => $labels,
            ])>
                <div class="relative w-full">
                    <x-rapidez::input
                        required
                        type="number"
                        v-bind:disabled="!canRefine"
                        v-bind:min="rangeInput.range.min"
                        v-bind:max="rangeInput.range.max"
                        v-model.lazy="rangeInput.minValue"
                        v-on:change="rangeInput.updateRefinement"
                        class="text-center arrows-hidden"
                        aria-label="@lang('Price from')"
                    />
                    <span
                        v-if="rangeInput.prefix"
                        class="absolute bottom-1/2 translate-y-1/2 font-light text-muted left-3"
                    >
                        @{{ rangeInput.prefix }}
                    </span>
                    <span
                        v-if="rangeInput.suffix"
                        class="absolute bottom-1/2 translate-y-1/2 font-light text-muted right-3"
                    >
                        @{{ rangeInput.suffix }}
                    </span>
                </div>
                <div class="relative w-full">
                    <x-rapidez::input
                        type="number"
                        v-bind:disabled="!canRefine"
                        v-bind:min="rangeInput.range.min"
                        v-bind:max="rangeInput.range.max"
                        v-model.lazy="rangeInput.maxValue"
                        v-on:change="rangeInput.updateRefinement"
                        class="text-center arrows-hidden"
                        aria-label="@lang('Price to')"
                    />
                    <span
                        v-if="rangeInput.prefix"
                        class="absolute bottom-1/2 translate-y-1/2 font-light text-muted left-3"
                    >
                        @{{ rangeInput.prefix }}
                    </span>
                    <span
                        v-if="rangeInput.suffix"
                        class="absolute bottom-1/2 translate-y-1/2 font-light text-muted right-3"
                    >
                        @{{ rangeInput.suffix }}
                    </span>
                </div>
            </div>
        @endif
    </div>
</range-slider>
