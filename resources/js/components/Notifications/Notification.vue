<template>
    <transition
        enter-active-class="ease-in-out duration-500"
        enter-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="ease-in-out duration-500"
        leave-class="opacity-100"
        leave-to-class="opacity-0"
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
                                {{ message }}
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
</template>

<script>
    export default {
        props: ['notification'],
        mounted() {
            this.message = this.notification.message
            this.type = this.notification.type
            this.show = this.notification.show

            setTimeout(() => {
                this.show = false
            }, 5000)
        },
        data: () => ({
            message: null,
            show: null,
            classTypes: {
                'success': 'text-green-600 bg-green-100 border border-green-300',
                'error': 'text-red-600 bg-red-100 border border-red-300',
                'info': 'text-blue-600 bg-blue-100 border border-blue-300',
                'warning': 'text-yellow-600 bg-yellow-100 border border-yellow-300'
            },
            iconTypes: {
                'success': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'error': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'info': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'warning': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
            }
        }),
        methods: {
            close() {
                this.$parent.notifications.splice(this.notification, 1)
            }
        },
        computed: {
            classType() {
                return this.classTypes[this.type]
            },

            icon() {
                return this.iconTypes[this.type]
            }
        }
    }
</script>
