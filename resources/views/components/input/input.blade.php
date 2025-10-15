<input
    v-bind:disabled="$root.loading"
    {{ $attributes->twMerge('w-full py-3 px-5 border border-default rounded-md outline-0 ring-0 text-sm transition-colors focus:ring-transparent focus:border-emphasis disabled:cursor-not-allowed placeholder:text-muted [&::-webkit-inner-spin-button]:appearance-none [&:user-invalid:not(:placeholder-shown)]:border-red-500') }} 
/>
