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
        options: {
            type: Object,
            default: {},
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

    methods: {
        getTaxPercent(product) {
            let taxClass = product.tax_class_id ?? product
            let taxValues = product.tax_values ?? window.config.tax.values[taxClass] ?? {}

            let groupId = this.$root.user?.group_id ?? 0 // 0 is always the NOT_LOGGED_IN group
            let customerTaxClass = window.config.tax.groups[groupId] ?? 0

            return taxValues[customerTaxClass] ?? 0
        },

        calculatePrice(product, location, options = {}) {
            let special_price = options.special_price ?? false
            let displayTax = this.$root.includeTaxAt(location);

            let price = special_price
                ? product.special_price ?? product.price ?? 0
                : product.price ?? 0

            if(options.product_options) {
                price += this.calculateOptionsValue(price, product, options.product_options)
            }

            let taxMultiplier = this.getTaxPercent(product) + 1

            if (window.config.tax.calculation.price_includes_tax == displayTax) {
                return price
            }

            return displayTax
                ? price * taxMultiplier
                : price / taxMultiplier
        },

        calculateOptionsValue(basePrice, product, customOptions) {
            let addition = 0

            Object.entries(customOptions).forEach(([key, val]) => {
                if (!val) {
                    return
                }

                let option = product.options.find((option) => option.option_id == key)
                let optionPrice = ['drop_down'].includes(option.type)
                    ? option.values.find((value) => value.option_type_id == val).price
                    : option.price

                if (optionPrice.price_type == 'fixed') {
                    addition += parseFloat(optionPrice.price)
                } else {
                    addition += (parseFloat(basePrice) * parseFloat(optionPrice.price)) / 100
                }
            })

            return addition
        }
    },

    computed: {
        specialPrice() {
            if(!this.mounted) {
                return;
            }
            return this.calculatePrice(this.product, this.type, Object.assign(this.options, { special_price: true }))
        },

        price() {
            if(!this.mounted) {
                return;
            }
            return this.calculatePrice(this.product, this.type, this.options)
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
                (child) => this.calculatePrice(child, this.type, Object.assign(this.options, { special_price: true }))
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
