<script>
    export default {
        render() {
            return this.$scopedSlots.default({
                close: this.close,
                accept: this.accept,
            })
        },
        props: {
            name: {
                type: String,
                required: true,
            },
            showUntilClose: {
                type: Boolean,
                default: false,
            },
            showOnce: {
                type: Boolean,
                default: false,
            },
        },
        methods: {
            open() {
                this.$el.show()
            },
            accept() {
                document.cookie = 'accept-cookies=true'
                this.close()
                location.reload()
            },
            close() {
                this.$el.close()
                if (this.showUntilClose) {
                    localStorage.setItem(this.name, true)
                }
            },
        },
        mounted() {
            if (!localStorage.getItem(this.name)) {
                this.open()
            }
        },
    }
</script>