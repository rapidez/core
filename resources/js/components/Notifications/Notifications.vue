<script>
export default {
    data: () => ({
        notifications: [],
    }),
    render() {
        return this.$scopedSlots.default({
            notifications: this.notifications,
        })
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
