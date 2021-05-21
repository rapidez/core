<script>
    export default {
        data: () => ({
            notifications: []
        }),
        render() {
            return this.$scopedSlots.default({
                notifications: this.notifications
            })
        },
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
