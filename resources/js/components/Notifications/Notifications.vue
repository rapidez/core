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
            this.$root.$on('notification-message', (message, type, params) => {
                this.notifications.push({
                    message: message,
                    params: params,
                    type: type,
                    show: true
                })
            });
        },
    }
</script>
