<cookie-notice
    name="cookie-notice"
    show-until-close
    v-cloak
>
    <div
        class="fixed inset-x-0 bottom-0 z-30 pb-2"
        slot-scope="{ show, close, accept }"
        v-if="show"
    >
        <div class="container">
            <div class="rounded bg-white p-6 shadow">
                <div class="flex flex-wrap items-center justify-between">
                    <div class="flex-1 items-center">
                        <div class="cookie-consent__message text-sm text-black">
                            @lang('This website uses cookies')
                        </div>
                    </div>
                    <x-rapidez::button @click="accept" variant="outline" class="mt-3 w-full shrink-0 md:ml-6 md:w-auto">
                        @lang('Accept cookies')
                    </x-rapidez::button>
                </div>
            </div>
        </div>
    </div>
</cookie-notice>