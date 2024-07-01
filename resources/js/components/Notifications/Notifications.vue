<script>
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
        this.$root.$on('notification-message', (message, type, params, link) => {
            this.notifications.push({
                message: message,
                type: type,
                params: params,
                link: link,
                show: true,
            })
        })
    },
}
</script>
