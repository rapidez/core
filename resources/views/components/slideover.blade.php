@props(['mobileOnly' => false])

<toggler v-slot="{ isOpen, toggle, close }">
    <div>
        {{ $button }}

        <div class="fixed inset-0 overflow-hidden z-30 {{ $mobileOnly ? 'md:static md:block' : '' }}" :class="isOpen ? 'pointer-events-auto' : 'pointer-events-none'">
            <div class="absolute inset-0 overflow-hidden {{ $mobileOnly ? 'md:static' : '' }}">
                <transition
                    enter-active-class="ease-in-out duration-500"
                    enter-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in-out duration-500"
                    leave-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-show="isOpen" v-on:click="toggle" class="absolute inset-0 pointer-events-auto bg-gray-500 bg-opacity-75 transition-opacity {{ $mobileOnly ? 'md:!hidden' : '' }}"></div>
                </transition>
                <section class="absolute inset-y-0 right-0 max-w-full flex pointer-events-auto {{ $mobileOnly ? 'md:static md:pl-0' : '' }}" :class="isOpen ? 'pl-10' : ''">
                    <transition
                        enter-active-class="transform transition ease-in-out duration-500 sm:duration-700"
                        enter-class="translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-active-class="transform transition ease-in-out duration-500 sm:duration-700"
                        leave-class="translate-x-0"
                        leave-to-class="translate-x-full"
                    >
                        <div v-show="isOpen" class="w-screen max-w-md {{ $mobileOnly ? 'md:!block' : '' }}">
                            <div class="h-full flex flex-col space-y-6 py-6 bg-white shadow-xl overflow-y-scroll {{ $mobileOnly ? 'md:py-0 md:space-y-0' : '' }}">
                                <header class="px-4 {{ $mobileOnly ? 'md:hidden' : '' }}">
                                    <div class="flex items-start justify-between space-x-3">
                                        <h2 class="text-lg leading-7 font-medium text-gray-900">
                                            {{ $title }}
                                        </h2>
                                        <div class="h-7 flex items-center">
                                            <button aria-label="@lang('Close filters')" class="text-gray-400 hover:text-gray-500 transition ease-in-out duration-150" v-on:click="toggle">
                                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </header>
                                <div class="relative flex-1 px-4 {{ $mobileOnly ? 'md:pl-0' : '' }}">
                                    {{ $slot }}
                                </div>
                            </div>
                        </div>
                    </transition>
                </section>
            </div>
        </div>
    </div>
</toggler>
