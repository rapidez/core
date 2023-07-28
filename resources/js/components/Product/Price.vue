<script>
export default {
    props: {
        product: {
            type: Object,
            default: () => config.product || {},
        },
        type: {
            type: String,
            default: 'catalog',
        },
    },
    render() {
        return this.$scopedSlots.default(Object.assign(this, { self: this }))
    },

    data: () => ({
        mounted: false,
    }),

    mounted() {
        this.mounted = true
    },

    computed: {
        specialPrice() {
            if(!this.mounted) {
                return;
            }
            return this.$root.calculatePrice(this.product, this.type, { special_price: true })
        },

        price() {
            if(!this.mounted) {
                return;
            }
            return this.$root.calculatePrice(this.product, this.type)
        },

        isDiscounted() {
            if(!this.mounted) {
                return false;
            }

            return this.specialPrice != this.price
        },

        range() {
            if (!this.product.super_attributes) {
                return null
            }

            let prices = Object.values(this.product.children).map(
                (child) => this.$root.calculatePrice(child, this.type, { special_price: true })
            )

            return {
                min: Math.min(...prices),
                max: Math.max(...prices),
            }
        },

        includesTax() {
            return this.$root.includeTaxAt(this.type)
        },
    },
}
</script>
