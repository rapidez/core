<script>
import { useLocalStorage } from '@vueuse/core'
export default {
    render() {
        return this.$slots.default(this)
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
            this.$el.nextSibling.show()
            if (this.overlay) {
                this.$root.custom.overlay = true
            }
        },
        close() {
            this.$el.nextSibling.close()

            if (this.overlay) {
                this.$root.custom.overlay = false
            }

            if (this.showUntilClose) {
                useLocalStorage(this.name, false).value = true
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

            if ((this.showOnce || this.showUntilClose) && !useLocalStorage(this.name, false).value) {
                this.open()

                if (this.showOnce) {
                    useLocalStorage(this.name, false).value = true
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
