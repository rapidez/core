@props(['inputs' => true])

<range-slider v-slot="rangeInputScope" {{ $attributes }}>
    <div class="w-full">
        <div class="flex flex-1 relative h-8">
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

            <div class="relative z-10 h-1 w-full mt-2 mx-2">
                <div class="absolute z-10 inset-0 rounded-md bg-border"></div>
                <div class="absolute z-20 inset-y-0 rounded-md bg-primary" v-bind:style="'right:'+rangeInputScope.maxThumb+'%; left:'+rangeInputScope.minThumb+'%'"></div>
                <div class="absolute z-30 size-5 top-0 left-0 bg-white border rounded-full -mt-2 -translate-x-1/2" v-bind:style="'left: '+rangeInputScope.minThumb+'%'"></div>
                <div class="absolute z-30 size-5 top-0 right-0 bg-white border rounded-full -mt-2 translate-x-1/2" v-bind:style="'right: '+rangeInputScope.maxThumb+'%'"></div>
                @if (!$inputs)
                    <div class="absolute z-30 top-4 left-0 bg-white border px-2 py-0.5 rounded-full -translate-x-1/2" v-bind:style="'left: '+rangeInputScope.minThumb+'%'">
                        @{{ rangeInputScope.minValue }}
                    </div>
                    <div class="absolute z-30 top-4 right-0 bg-white border px-2 py-0.5 rounded-full translate-x-1/2" v-bind:style="'right: '+rangeInputScope.maxThumb+'%'">
                        @{{ rangeInputScope.maxValue }}
                    </div>
                @endif
            </div>
        </div>
        @if ($inputs)
            <div class="flex items-center gap-x-5">
                <x-rapidez::input
                    type="number"
                    v-bind:disabled="!canRefine"
                    v-bind:min="rangeInputScope.range.min"
                    v-bind:max="rangeInputScope.range.max"
                    v-model="rangeInputScope.minValue"
                    v-on:input="rangeInputScope.updateRefinement"
                    class="text-center"
                />
                <x-rapidez::input
                    type="number"
                    v-bind:disabled="!canRefine"
                    v-bind:min="rangeInputScope.range.min"
                    v-bind:max="rangeInputScope.range.max"
                    v-model="rangeInputScope.maxValue"
                    v-on:input="rangeInputScope.updateRefinement"
                    class="text-center"
                />
            </div>
        @endif
    </div>
</range-slider>
