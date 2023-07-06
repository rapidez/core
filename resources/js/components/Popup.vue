<script>
export default {
    render() {
        return this.$scopedSlots.default({
            close: this.close,
        })
    },
    props: {
        name: {
            type: String,
            required: true,
        },
        duration: {
            type: Number,
        },
        delay: {
            type: Number,
            default: 0,
        },
        showOnce: {
            type: Boolean,
            default: false,
        },
        showUntilClose: {
            type: Boolean,
            default: false,
        },
        overlay: {
            type: Boolean,
            default: false,
        },
        callback: {
            type: Function,
        },
    },
    methods: {
        open() {
            this.$el.show()
            if (this.overlay) {
                this.$root.custom.overlay = true
            }
        },
        close() {
            this.$el.close()

            if (this.overlay) {
                this.$root.custom.overlay = false
            }

            if (this.showUntilClose) {
                localStorage.setItem(this.name, true)
            }

            if (this.callback) {
                this.callback()
            }
        },
    },
    mounted() {
        setTimeout(() => {
            if (!this.showOnce && !this.showUntilClose) {
                this.open()
            }

            if ((this.showOnce || this.showUntilClose) && !localStorage.getItem(this.name)) {
                this.open()

                if (this.showOnce) {
                    localStorage.setItem(this.name, true)
                }
            }

            if (this.duration > 1) {
                setTimeout(() => {
                    this.close()
                }, this.duration)
            }
        }, this.delay)
    },
}
</script>