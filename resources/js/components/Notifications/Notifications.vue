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
        window.$on('rapidez:notification-message', (message, type, params, link) => {
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
