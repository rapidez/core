<script>
import { pendingNotifications, clearNotifications, notificationCount } from '../../stores/useNotifications.js'
import { watch } from 'vue'

import notification from './Notification.vue'
Vue.component('notification', notification)

export default {
    data: () => ({
        notifications: [],
    }),
    render() {
        return this.$scopedSlots.default(this)
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
        },
    },
}
</script>
