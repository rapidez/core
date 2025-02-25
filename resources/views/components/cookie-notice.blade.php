@if (!App::runningUnitTests() || request()->has('show-cookie-notice'))
    <popup
        name="cookie-notice"
        :callback="() => {
            window.$cookies.set('accept-cookies', 'true')
            window.location.reload();
        }"
        show-until-close
        v-cloak
    >
        <dialog slot-scope="{ close }" class="container rounded-sm bg-white p-6 shadow-sm fixed inset-x-0 bottom-4 z-30">
            <div class="flex flex-wrap items-center justify-between">
                <div class="flex-1 items-center">
                    <div class="text-sm text-black">
                        {{ $slot }}
                    </div>
                </div>
                <x-rapidez::button.outline @click="close" class="mt-3 w-full shrink-0 md:ml-6 md:w-auto">
                    {{ $button ?? __('Accept cookies') }}
                </x-rapidez::button.outline>
            </div>
        </dialog>
    </popup>
@endif
