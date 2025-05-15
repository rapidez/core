@props(['inputs' => true, 'price' => false])
@slots(['value'])

<range-slider v-slot="{ _renderProxy: rangeInputScope, value }" {{ $attributes }}>
    <div class="w-full">
        <div @class([
                'flex flex-1 relative',
                'h-12 mx-1' => !$inputs
            ])
        >
            <input
                type="range"
                v-bind:disabled="!canRefine"
                v-bind:min="rangeInputScope.range.min"
                v-bind:max="rangeInputScope.range.max"
                v-model="rangeInputScope.minValue"
                v-on:change="rangeInputScope.updateRefinement"
                class="absolute pointer-events-none appearance-none z-20 h-5 w-full opacity-0 cursor-pointer [&::-moz-range-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:pointer-events-auto"
            >

            <input
                type="range"
                v-bind:disabled="!canRefine"
                v-bind:min="rangeInputScope.range.min"
                v-bind:max="rangeInputScope.range.max"
                v-model="rangeInputScope.maxValue"
                v-on:change="rangeInputScope.updateRefinement"
                class="absolute pointer-events-none appearance-none z-20 h-5 w-full opacity-0 cursor-pointer [&::-moz-range-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:pointer-events-auto"
            >

            <div class="relative z-10 w-full mx-2 h-4">
                <div class="absolute top-1/2 -translate-y-1/2 h-1 z-10 -inset-x-2 rounded-md bg-emphasis"></div>
                <div class="absolute top-1/2 -translate-y-1/2 h-1 z-20 rounded-md bg-active" v-bind:style="'right:'+rangeInputScope.maxThumb+'%; left:'+rangeInputScope.minThumb+'%'"></div>
                <div class="absolute z-30 size-4 top-1/2 left-0 bg-active rounded-full -translate-y-1/2 -translate-x-1/2" v-bind:style="'left: '+rangeInputScope.minThumb+'%'"></div>
                <div class="absolute z-30 size-4 top-1/2 right-0 bg-active rounded-full -translate-y-1/2 translate-x-1/2" v-bind:style="'right: '+rangeInputScope.maxThumb+'%'"></div>
                @if (!$inputs)
                    <div
                        class="absolute z-30 top-5 left-0 bg-active text-white px-2 py-0.5 rounded-md -translate-x-1/2"
                        v-bind:style="'left: '+rangeInputScope.minThumb+'%'"
                        v-bind:set="value = rangeInputScope.minValue"
                    >
                        @if ($value->isNotEmpty())
                            {{ $value }}
                        @else
                            <span v-text="value"></span>
                        @endif
                    </div>
                    <div
                        class="absolute z-30 top-5 right-0 bg-active text-white px-2 py-0.5 rounded-md translate-x-1/2"
                        v-bind:style="'right: '+rangeInputScope.maxThumb+'%'"
                        v-bind:set="value = rangeInputScope.maxValue"
                    >
                        @if ($value->isNotEmpty())
                            {{ $value }}
                        @else
                            <span v-text="value"></span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @if ($inputs)
            <div class="flex items-center gap-x-4 mt-4">
                @if ($price)
                    <div class="relative w-full">
                        <x-rapidez::input
                            required
                            class="text-center arrows-hidden"
                            type="number"
                            v-bind:min="range.min"
                            v-bind:max="range.max"
                            v-bind:placeholder="range.min"
                            v-bind:disabled="!canRefine"
                            v-bind:value="currentRefinement.min ?? range.min"
                            v-on:input="if (!$event.currentTarget.validationMessage) {
                                refine({ min: Math.max($event.currentTarget.value, range.min), max: currentRefinement.max })
                            }"
                        />
                        <span
                            class="absolute bottom-1/2 translate-y-1/2 font-light text-muted"
                            v-bind:class="{
                                'left-3': $root.currencySymbolLocation === 'left',
                                'right-3': $root.currencySymbolLocation === 'right',
                            }"
                        >
                            @{{ $root.currencySymbol }}
                        </span>
                    </div>
                    <div class="relative w-full">
                        <x-rapidez::input
                            required
                            class="text-center arrows-hidden"
                            type="number"
                            v-bind:min="range.min"
                            v-bind:max="range.max"
                            v-bind:placeholder="range.max"
                            v-bind:disabled="!canRefine"
                            v-bind:value="currentRefinement.max ?? range.max"
                            v-on:input="if (!$event.currentTarget.validationMessage) {
                                refine({ min: currentRefinement.min, max: Math.min($event.currentTarget.value, range.max) })
                            }"
                        />
                        <span
                            class="absolute bottom-1/2 translate-y-1/2 font-light text-muted"
                            v-bind:class="{
                                'left-3': $root.currencySymbolLocation === 'left',
                                'right-3': $root.currencySymbolLocation === 'right',
                            }"
                        >
                            @{{ $root.currencySymbol }}
                        </span>
                    </div>
                @else
                    <x-rapidez::input
                        type="number"
                        v-bind:disabled="!canRefine"
                        v-bind:min="rangeInputScope.range.min"
                        v-bind:max="rangeInputScope.range.max"
                        v-model="rangeInputScope.minValue"
                        v-on:input="rangeInputScope.updateRefinement"
                        class="text-center arrows-hidden"
                    />
                    <x-rapidez::input
                        type="number"
                        v-bind:disabled="!canRefine"
                        v-bind:min="rangeInputScope.range.min"
                        v-bind:max="rangeInputScope.range.max"
                        v-model="rangeInputScope.maxValue"
                        v-on:input="rangeInputScope.updateRefinement"
                        class="text-center arrows-hidden"
                    />
                @endif
            </div>
        @endif
    </div>
</range-slider>
