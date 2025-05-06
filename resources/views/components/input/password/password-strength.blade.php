@slots(['label'])

<div {{ $attributes->only('class')->class('') }}>
    <x-rapidez::label class="mb-2 text-inactive">
        {{ $label }}
    </x-rapidez::label>
    <password-strength
        v-slot="passwordStrength"
        v-bind:min-length="{{ Rapidez::config('customer/password/minimum_password_length', 8) }}"
        v-bind:min-classes="{{ Rapidez::config('customer/password/required_character_classes_number', 3) }}"
        {{ $attributes }}
    >
        <div class="relative rounded-lg sm:border sm:p-6 sm:shadow lg:p-8">
            <p v-if="passwordStrength.minClasses < 4" class="text-xs text-inactive mb-2">
                @lang('Password must have :minClasses differrent characters', ['minClasses' => "@{{ passwordStrength.minClasses }}" ])
            </p>
            <div class="mb-4 h-2.5 w-full rounded-full bg-gray-200">
                <div
                    v-bind:style="`width:${passwordStrength.strengths.length / 5 * 100}%`"
                    v-bind:class="passwordStrength.errors.length > 0 ? 'bg-red-500' : 'bg-green-700 !w-full'"
                    class="h-2.5 rounded-full transition-all duration-300"
                ></div>
            </div>
            <div v-for="error in passwordStrength.errors" class="my-1 flex items-center gap-x-2">
                <div class="flex size-4 shrink-0 items-center justify-center rounded-full bg-red-500 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 text-red-500">
                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm2.78-4.22a.75.75 0 0 1-1.06 0L8 9.06l-1.72 1.72a.75.75 0 1 1-1.06-1.06L6.94 8 5.22 6.28a.75.75 0 0 1 1.06-1.06L8 6.94l1.72-1.72a.75.75 0 1 1 1.06 1.06L9.06 8l1.72 1.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-sm text-red-500"> @{{ error }}</p>
            </div>
            <div v-for="strength in passwordStrength.strengths" class="my-1 flex items-center gap-x-2">
                <div class="flex size-4 shrink-0 items-center justify-center rounded-full bg-green-700 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 text-green-700">
                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm3.844-8.791a.75.75 0 0 0-1.188-.918l-3.7 4.79-1.649-1.833a.75.75 0 1 0-1.114 1.004l2.25 2.5a.75.75 0 0 0 1.15-.043l4.25-5.5Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="text-sm text-green-700"> @{{ strength }}</p>
            </div>
        </div>
    </password-strength>
</div>
