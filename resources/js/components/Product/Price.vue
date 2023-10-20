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
        calculatePrice(options = null) {
            options ??= this.options;
            let product = options?.product || this.product;
            let location = options?.location || this.location;

            let special_price = options.special_price ?? false
            let displayTax = this.$root.includeTaxAt(location)
            let price = options?.price || (special_price ? product.special_price ?? product.price ?? 0 : product.price ?? 0) * 1

            if (options.tier_price) {
                price = options.tier_price.price * 1
            }

            if (options.product_options) {
                price += this.calculateOptionsValue(price, product, options.product_options)
            }

            let taxMultiplier = this.getTaxPercent(product) + 1

            return displayTax ? price * taxMultiplier : price / taxMultiplier
        },

        getTaxPercent(product) {
            let country_id = window.config.tax.defaults.country_id
            let region_id = window.config.tax.defaults.region_id
            let postcode = window.config.tax.defaults.postcode

            if (['shipping', 'billing'].includes(window.config?.tax?.calculation.based_on)) {
                country_id =  window.app?.$data?.checkout?.[window.config?.tax?.calculation.based_on + '_address']?.country_id || country_id
                region_id =  window.app?.$data?.checkout?.[window.config?.tax?.calculation.based_on + '_address']?.region_id || region_id
                postcode =  window.app?.$data?.checkout?.[window.config?.tax?.calculation.based_on + '_address']?.postcode || postcode
            }

            let taxRate = window.config.tax.rates?.[product.tax_class_id]?.find(
                (rate) =>
                    rate.tax_country_id === country_id &&
                    rate.tax_region_id * 1 === region_id * 1 &&
                    postcode.match('^' + rate.tax_postcode.replace('*', '.*') + '$'),
            )?.rate

            if (taxRate === undefined || taxRate === null) {
                console.debug('No tax rates found for', product, country_id, region_id, postcode)
            }

            return (taxRate ?? 0) / 100
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
                    return priceAddition * 1 + parseFloat(optionPrice.price)
                }

                return priceAddition * 1 + (parseFloat(basePrice) * parseFloat(optionPrice.price)) / 100
            }, 0)
        },
    },

    computed: {
        specialPrice() {
            JSON.stringify(this.options) // Hack: make vue recognize reactivity within the options object
            return this.calculatePrice({...this.options, ...{ special_price: true }})
        },

        price() {
            return this.calculatePrice(this.options)
        },

        isDiscounted() {
            return this.specialPrice != this.price
        },

        range() {
            if (!this.product.super_attributes) {
                return null
            }

            let prices = Object.values(this.product.children).map((child) =>
                this.calculatePrice({...this.option, ...{ special_price: true, product: child }}),
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
