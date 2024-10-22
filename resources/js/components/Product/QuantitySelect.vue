<script>
export default {
    props: {
        inputRef: {
            type: String,
            default: 'qty-select-1',
        },
        defaultQty: {
            type: Number,
            default: 1,
        },
        minQty: {
            type: Number,
            default: 1,
        },
        model: {
            type: Number,
            default: 1,
        },
        increment: {
            type: Number,
            default: 1,
        },
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    methods: {
        increase() {
            this.updateQty(this.model + this.increment)
        },

        decrease() {
            if (this.model - this.increment >= this.minQty) {
                this.updateQty(this.model - this.increment)
            }
        },

        updateQty(qty) {
            let input = this.$scopedSlots.default()[0].context.$refs[this.inputRef]
            if (Array.isArray(input)) {
                input = input[0]
            }

            input.value = qty
            let event = new Event('input')
            input.dispatchEvent(event)
            this.$root.$emit('updated-quantity')
        },
    },
}
</script>
