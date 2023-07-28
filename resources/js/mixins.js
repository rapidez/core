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

        includeTaxAt(location) {
            return location === true || location === false
                ? location
                : (window.config.tax.display[location] ?? 0) >= 1
        },

        decideTax(including, excluding, location) {
            return this.includeTaxAt(location) ? including : excluding
        },
    },
})
