<script>
export default {
    props: {
        notification: {
            type: Object,
        },
        duration: {
            type: Number,
            default: 5000,
        },
    },
    render() {
        return this.$slots.default(this)
    },
    created() {
        this.message = this.notification.message.includes('%')
            ? this.format(this.notification.message, this.notification.params)
            : this.notification.message
        this.show = this.notification.show
        this.type = this.notification.type
        this.link = this.notification.link
    },
    mounted() {
        setTimeout(
            () => {
                this.close()
            },
            Math.max(this.duration, (this.message.split(' ').length / 3) * 1000),
        )
    },
    data: () => ({
        message: null,
        type: null,
        show: null,
        link: null,
    }),
    methods: {
        close() {
            this.show = false
        },
        format(str, arr) {
            return (
                str
                    // Replace indexed variables
                    .replace(/%(\d+)/g, (_, m) => arr[--m])
                    // Replace named variables
                    .replace(/%(?<fieldName>\w+)/g, (match, fieldName) => arr[fieldName] ?? '%' + fieldName)
            )
        },
    },
    computed: {
        classes() {
            return window.config.notifications.classes[this.type]
        },
    },
}
</script>
