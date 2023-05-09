<script>
export default {
    props: {
        product: {
            type: Object,
            default: () => window.config.product || {},
        },
        type: {
            type: String,
            default: 'catalog'
        },
        tax: {
            type: Number,
            default: () => window.config.product.tax
        }
    },
    render() {
        return this.$scopedSlots.default({
            price: this.price,
            specialPrice: this.specialPrice
        });
    },

    data: () => ({
        mounted: false,
    }),

    mounted() {
        this.mounted = true;
    },

    methods: {
        displayTax() {
            return window.config.tax.display[this.type] > 1;
        },
        calculateTax(price, subtract = false) {
            return subtract
                ? price / (this.tax + 100) * 100
                : price * (this.tax + 100) / 100
        },
        getPriceInclTax() {
            if (window.config.tax.calculation.price_includes_tax === '1') {
                return this.product.price
            }

            return this.calculateTax(this.product.price)
        },
        getPriceExclTax() {
            if (window.config.tax.calculation.price_includes_tax === '0') {
                return this.product.price
            }

            return this.calculateTax(this.product.price, true)
        },
        getSpecialPriceInclTax() {
            if (this.product.special_price === null || window.config.tax.calculation.price_includes_tax === '1') {
                return this.product.special_price
            }

            return this.calculateTax(this.product.special_price)
        },
        getSpecialPriceExclTax() {
            if (this.product.special_price === null || window.config.tax.calculation.price_includes_tax === '0') {
                return this.product.special_price
            }

            return this.calculateTax(this.product.special_price, true)
        }
    },

    computed: {
        price() {
            if (this.mounted) {
                if (this.displayTax()) {
                    return this.getPriceInclTax()
                }

                return this.getPriceExclTax()
            }
        },
        specialPrice() {
            if (this.mounted) {
                if (this.displayTax()) {
                    return this.getSpecialPriceInclTax()
                }

                return this.getSpecialPriceExclTax()
            }
        }
    }
}
</script>
