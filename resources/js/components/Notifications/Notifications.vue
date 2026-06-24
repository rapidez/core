<script>
import { pendingNotifications, clearNotifications, notificationCount } from '../../stores/useNotifications.js'
import { watch } from 'vue'

import notification from './Notification.vue'
document.addEventListener('vue:loaded', function (event) {
    event.detail.vue.component('notification', notification)
})
export default {
    components: {
        notification: notification,
    },
    data: () => ({
        notifications: [],
    }),
    render() {
        return this.$slots.default(this)
    },
    mounted() {
        this.triggerNotifications()
        watch(notificationCount, this.triggerNotifications)
    },
    methods: {
        triggerNotifications() {
            if (notificationCount.value == 0) {
                return
            }

            pendingNotifications.value.forEach((notification) => {
                this.notifications.push({
                    show: true,
                    ...notification,
                })
            })

            clearNotifications()
        }
    },
}
</script>
