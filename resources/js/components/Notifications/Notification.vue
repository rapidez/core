<script>
    export default {
        props: {
            notification: {
                type: Object
            },
            duration: {
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
                link: this.link
            })
        },
        created() {
            this.message = this.notification.message.includes('%') ? this.format(this.notification.message, this.notification.params) : this.notification.message
            this.show = this.notification.show
            this.type = this.notification.type
            this.link = this.notification.link
        },
        mounted() {
            setTimeout(() => {
                this.close()
            }, this.duration)
        },
        data: () => ({
            message: null,
            type: null,
            show: null,
            link: null
        }),
        methods: {
            close() {
                this.show = false
            },
            format(str, arr) {
                return str.replace(/%(\d+)/g, function(_,m) {
                    return arr[--m];
                });
            }

        },
        computed: {
            classes() {
                return window.config.notifications.classes[this.type]
            },
        }
    }
</script>
