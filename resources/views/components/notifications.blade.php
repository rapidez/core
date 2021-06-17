<notifications v-cloak>
    <div class="fixed sm:max-w-sm sm:w-full top-6 right-6 left-6 sm:left-auto flex flex-col z-20" slot-scope="{ notifications }">
        <notification v-if="notifications.length" v-for="(notification, index) in notifications" :notification="notification" :key="index +1">
            <transition
                enter-active-class="ease-in-out duration-500"
                enter-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="ease-in-out duration-500"
                leave-class="opacity-100"
                leave-to-class="opacity-0"
                slot-scope="{ message, type, show, close, classes}"
            >
                <div v-if="show" class="relative flex items-end justify-center pointer-events-none mb-3 sm:items-start sm:justify-end z-20">
                    <div class="max-w-sm w-full shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden border" :class="classes">
                        <div class="p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-check-circle class="h-6 w-6" v-if="type == 'success'"/>
                                    <x-heroicon-o-exclamation-circle class="h-6 w-6" v-if="type == 'error'"/>
                                    <x-heroicon-o-information-circle class="h-6 w-6" v-if="type == 'info'"/>
                                    <x-heroicon-o-exclamation class="h-6 w-6" v-if="type == 'warning'"/>
                                </div>
                                <div class="ml-3 w-0 flex-1 pt-0.5">
                                    <p class="text-sm font-medium">
                                        @{{ message }}
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0 flex self-start">
                                    <button @click.prevent="close()" class="rounded-md inline-flex focus:outline-none focus:ring-none focus:ring-offset-none">
                                        <span class="sr-only">@lang('Close')</span>
                                        <x-heroicon-s-x class="h-5 w-5"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </notification>
    </div>
</notifications>
