<template>
    <div class="fixed max-w-sm w-full bottom-6 left-6 flex flex-col z-20">
        <notification v-for="(notification, index) in notifications" :notification="notification" :key="index +1"></notification>
    </div>
</template>
<script>
    export default {
        data: () => ({
            notifications: []
        }),
        mounted() {
            Bus.$on('notification-message', (message, type) => {
                this.notifications.push({
                    message: message,
                    type: type,
                    show: true
                })
            });
        },
    }
</script>
