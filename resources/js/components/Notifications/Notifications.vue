<script>
import notification from './Notification.vue'
document.addEventListener('vue:loaded', function (event) {
    event.detail.vue.component('notification', notification)
})
export default {
    components: {
        'notification': notification
    },
    data: () => ({
        notifications: [],
    }),
    render() {
        return this.$slots.default(this)
    },
    mounted() {
        document.addEventListener('rapidez:notification-message', (event) => {
            const {message, type, params, link} = event.detail
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
