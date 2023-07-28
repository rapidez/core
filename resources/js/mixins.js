Vue.mixin({
    methods: {
        async asyncForEach(array, callback) {
            for (let index = 0; index < array.length; index++) {
                await callback(array[index], index, array)
            }
        },

        async magentoCart(method, endpoint, data) {
            if (this.$root.loggedIn) {
                return await magentoUser[method]('carts/mine/' + endpoint, data)
            } else {
                return await magento[method]('guest-carts/' + localStorage.mask + '/' + endpoint, data)
            }
        },

        // Calculation methods for prices including and excluding tax
        includeTaxAt(location) {
            return location === true || location === false
                ? location
                : (window.config.tax.display[location] ?? 0) >= 1
        },

        calculatePrice(product, location, options = {}) {
            let total = options.total ?? false
            let special_price = options.special_price ?? false

            let displayTax = this.includeTaxAt(location);

            let price = special_price
                ? product.special_price ?? product.price ?? 0
                : product.price ?? 0

            if(options.product_options) {
                price += this.calculateOptionsValue(price, product, options.product_options)
            }

            let taxMultiplier = parseFloat(window.config.tax.values[product.tax_class_id] ?? product.tax_amount ?? 0) + 1
            let qty = total ? product.qty ?? 1 : 1

            if (window.config.tax.calculation.price_includes_tax == displayTax) {
                return qty * price
            }

            return displayTax
                ? qty * price * taxMultiplier
                : qty * price / taxMultiplier
        },

        decideTax(including, excluding, location) {
            return this.includeTaxAt(location) ? including : excluding
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
})
