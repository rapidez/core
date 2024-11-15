<script>
// This components acts like a normal input element.
// The props and events are the same so you're
// able to use v-model, min, step and max
export default {
    props: {
        value: {
            // We're not enforcing a type here as
            // the input could be set to empty.
            default: 1,
        },
        min: {
            type: Number,
            default: 0,
        },
        step: {
            type: Number,
            default: 1,
        },
        max: {
            type: Number,
            default: 0, // = disabled / no maximum
        },
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    methods: {
        increase() {
            if (this.increasable) {
                this.$emit('input', this.value + this.step)
                this.$emit('change')
            }
        },

        decrease() {
            if (this.decreasable) {
                this.$emit('input', this.value - this.step)
                this.$emit('change')
            }
        },
    },

    computed: {
        increasable() {
            if (!this.max) {
                return true
            }

            return this.value + this.step <= this.max
        },

        decreasable() {
            return this.value - this.step >= this.min
        },
    },
}
</script>
