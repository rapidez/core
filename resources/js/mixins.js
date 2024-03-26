import { mask } from './stores/useMask'

Vue.mixin({
    methods: {
        async asyncForEach(array, callback) {
            for (let index = 0; index < array.length; index++) {
                await callback(array[index], index, array)
            }
        },

        async magentoCart(method, endpoint, data) {
            if (this.$root.loggedIn) {
                return await window.magentoAPI(method, 'carts/mine/' + endpoint, data)
            } else {
                return await window.magentoAPI(method, 'guest-carts/' + mask.value + '/' + endpoint, data)
            }
        },
    },

    computed: {
        fixedProductTaxes() {
            let taxes = {}
            this.$root.cart.items.forEach(item => {
                item.prices.fixed_product_taxes.forEach(tax => {
                    if(tax.label in taxes) {
                        taxes[tax.label] += tax.amount.value
                    } else {
                        taxes[tax.label] = tax.amount.value
                    }
                })
            })
            return taxes
        }
    }
})
