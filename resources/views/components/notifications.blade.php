<notifications v-cloak>
    <div class="fixed sm:max-w-sm sm:w-full top-6 right-6 left-6 sm:left-auto flex flex-col {{ config('rapidez.frontend.z-indexes.notification') }}" slot-scope="notificationsScope">
        <notification v-if="notificationsScope.notifications.length" v-for="(notification, index) in notificationsScope.notifications" :notification="notification" :key="index + 1">
            <transition
                enter-active-class="ease-in-out duration-500"
                enter-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="ease-in-out duration-500"
                leave-class="opacity-100"
                leave-to-class="opacity-0"
                slot-scope="notificationScope"
            >
                <component :is="notificationScope.link ? 'a' : 'div'" v-if="notificationScope.show" class="relative flex items-end justify-center pointer-events-none mb-3 sm:items-start sm:justify-end {{ config('rapidez.frontend.z-indexes.notification') }}" :class="{ 'pointer-events-none': !notificationScope.link }">
                    <div class="max-w-sm w-full shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden border" :class="notificationScope.classes">
                        <div class="p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <x-heroicon-o-check-circle class="h-6 w-6" v-if="notificationScope.type == 'success'"/>
                                    <x-heroicon-o-exclamation-circle class="h-6 w-6" v-else-if="notificationScope.type == 'error'"/>
                                    <x-heroicon-o-information-circle class="h-6 w-6" v-else-if="notificationScope.type == 'info'"/>
                                    <x-heroicon-o-exclamation-triangle class="h-6 w-6" v-else-if="notificationScope.type == 'warning'"/>
                                </div>
                                <div class="ml-3 w-0 flex-1 pt-0.5">
                                    <p class="text-sm font-medium">
                                        @{{ notificationScope.message }}
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0 flex self-start">
                                    <button @click.prevent="notificationScope.close" class="rounded-md inline-flex focus:outline-none focus:ring-none focus:ring-offset-none">
                                        <span class="sr-only">@lang('Close')</span>
                                        <x-heroicon-s-x-mark class="h-5 w-5"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </component>
            </transition>
        </notification>
    </div>
</notifications>
