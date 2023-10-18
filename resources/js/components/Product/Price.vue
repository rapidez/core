<script>
export default {
    props: {
        product: {
            type: Object,
            default: () => config.product || {},
        },
        location: {
            type: String,
            default: 'catalog',
        },
        options: {
            type: Object,
            default: () => ({}),
        },
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    methods: {
        calculatePrice(product, location, options = {}) {
            let special_price = options.special_price ?? false
            let displayTax = this.$root.includeTaxAt(location)

            let price = (special_price ? product.special_price ?? product.price ?? 0 : product.price ?? 0) * 1

            if (options.tier_price) {
                price = options.tier_price.price * 1
            }

            if (options.product_options) {
                price += this.calculateOptionsValue(price, product, options.product_options)
            }

            let taxMultiplier = this.getTaxPercent(product) * 1 + 1

            if (window.config.tax.calculation.price_includes_tax == displayTax) {
                return price
            }

            return displayTax ? price * taxMultiplier : price / taxMultiplier
        },

        getTaxPercent(product) {
            let taxClass = product.tax_class_id ?? product
            let taxValues = product.tax_values ?? window.config.tax.values[taxClass] ?? {}

            // TODO: Figure out where to get the tax rate calculation from
            let groupId = this.$root.user?.group_id ?? 0 // 0 is always the NOT_LOGGED_IN group
            let customerTaxClass = window.config.tax.groups[groupId] ?? 0

            return taxValues[customerTaxClass] ?? 0
        },

        calculateOptionsValue(basePrice, product, customOptions) {
            return Object.entries(customOptions).reduce((priceAddition, [key, val]) => {
                if (!val) {
                    return priceAddition
                }

                let option = product.options.find((option) => option.option_id == key)
                let optionPrice = ['drop_down', 'radio'].includes(option.type)
                    ? option.values.find((value) => value.option_type_id == val).price
                    : option.price

                if (optionPrice.price_type == 'fixed') {
                    return (priceAddition * 1) + parseFloat(optionPrice.price)
                }

                return (priceAddition * 1) + (parseFloat(basePrice) * parseFloat(optionPrice.price)) / 100
            }, 0)
        },
    },

    computed: {
        specialPrice() {
            return this.calculatePrice(this.product, this.location, Object.assign({ special_price: true }, this.options))
        },

        price() {
            return this.calculatePrice(this.product, this.location, this.options)
        },

        isDiscounted() {
            return this.specialPrice != this.price
        },

        range() {
            if (!this.product.super_attributes) {
                return null
            }

            let prices = Object.values(this.product.children).map((child) =>
                this.calculatePrice(child, this.location, Object.assign({ special_price: true }, this.options)),
            )

            return {
                min: Math.min(...prices),
                max: Math.max(...prices),
            }
        },

        includesTax() {
            return this.$root.includeTaxAt(this.location)
        },
    },
}
</script>
