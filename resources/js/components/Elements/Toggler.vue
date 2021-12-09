<script>
    export default {
        render() {
            return this.$scopedSlots.default({
                isOpen: this.isOpen,
                toggle: this.toggle,
                close: this.close,
                closeAll: this.closeAll
            })
        },

        props: {
            open: {
                type: Boolean,
                default: false
            },
            callback: {
                type: Function,
            },
            refs: {
                type: String,
                default: 'toggler-'
            }
        },

        data: () => ({
            isOpen: false,
        }),

        mounted() {
            this.isOpen = this.open
        },

        methods: {
            closeAll() {
                let refs = Object.keys(this.$scopedSlots.default()[0].context.$refs).filter((ref) => {
                    return ref.includes(this.refs)
                })

                refs.forEach((ref) => {
                    this.$scopedSlots.default()[0].context.$refs[ref].close()
                })
            },

            toggle() {
                this.isOpen = !this.isOpen

                if (this.callback) {
                    this.callback(this.isOpen)
                }
            },

            close() {
                this.isOpen = false
            }
        }
    }
</script>
