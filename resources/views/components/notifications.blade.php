<notifications v-cloak>
    <div class="fixed max-w-sm w-full bottom-6 left-6 flex flex-col z-20" slot-scope="{ notifications }">
        <notification v-if="notifications.length" v-for="(notification, index) in notifications" :notification="notification" :key="index +1">
            <transition
            enter-active-class="ease-in-out duration-500"
            enter-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in-out duration-500"
            leave-class="opacity-100"
            leave-to-class="opacity-0"
            slot-scope="{ message, type, show, close, icon, classType}"
            >
            <div v-if="show" class="relative flex items-end justify-center pointer-events-none sm:mb-3 sm:items-start sm:justify-end z-20">
                <div class="max-w-sm w-full shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden" :class="classType">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true" v-html="icon">
                                </svg>
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium">
                                    @{{ message }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex self-start">
                                <button @click.prevent="close()" class="rounded-md inline-flex focus:outline-none focus:ring-none focus:ring-offset-none">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
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
