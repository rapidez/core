<script>
    export default {
        props: {
            notification: {
                type: Object
            },
            timeout: {
                type: Number,
                default: 5000
            }
        },
        render() {
            return this.$scopedSlots.default({
                classes: this.classes,
                close: this.close,
                message: this.message,
                show: this.show,
                type: this.type,
            })
        },
        created() {
            this.message = this.notification.message
            this.show = this.notification.show
            this.type = this.notification.type
        },
        mounted() {
            setTimeout(() => {
                this.close()
            }, this.timeout)
        },
        data: () => ({
            message: null,
            type: null,
            show: null,
        }),
        methods: {
            close() {
                this.show = false
            }
        },
        computed: {
            classes() {
                return window.config.notifications.classes[this.type]
            },
        }
    }
</script>
