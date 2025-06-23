<script>
export default {
    props: {
        range: {
            type: Object,
            default: () => ({ min: 0, max: 100 }),
        },
        prefix: {
            type: String
        },
        suffix: {
            type: String
        },
        current: {
            type: Object,
            default: () => ({ min: undefined, max: undefined }),
        },
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    data() {
        return {
            minValue: null,
            maxValue: null,
        }
    },

    watch: {
        minValue() {
            if (this.minValue >= this.maxValue) {
                this.minValue = this.maxValue - 1
            }
        },

        maxValue() {
            if (this.maxValue <= this.minValue) {
                this.maxValue = this.minValue + 1
            }
        },

        range() {
            if (this.range.min == this.range.max && this.range.min == 0) {
                return
            }

            if (this.minValue === null && this.maxValue === null) {
                this.updateFromProps()
            }
        },

        current() {
            this.updateFromProps()
        },
    },

    mounted() {
        this.updateFromProps()
    },

    methods: {
        updateFromProps() {
            this.minValue = this.current.min ?? this.range.min
            this.maxValue = this.current.max ?? this.range.max
        },

        percentage(amount) {
            return ((amount - this.range.min) / (this.range.max - this.range.min)) * 100
        },

        updateRefinement() {
            this.$emit('change', {
                min: this.minValue,
                max: this.maxValue,
            })
        },
    },

    computed: {
        minThumb() {
            return Math.max(this.percentage(this.minValue), 0)
        },

        maxThumb() {
            return Math.max(100 - this.percentage(this.maxValue), 0)
        },
    },
}
</script>
