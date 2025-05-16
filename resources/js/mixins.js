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

        attributeLabel(attributeCode) {
            return Object.values(window.config.attributes)?.find((attribute) => attribute.code === attributeCode)?.name
        },
    },

    computed: {
        currencySymbolLocation() {
            return new Intl.NumberFormat(config.locale.replace('_', '-'), {
                style: 'currency',
                currency: config.currency,
            }).formatToParts(1)?.[0]?.type === 'currency'
                ? 'left'
                : 'right'
        },

        currencySymbol() {
            return new Intl.NumberFormat(config.locale.replace('_', '-'), {
                style: 'currency',
                currency: config.currency,
                maximumFractionDigits: 0,
            })
                .format(0)
                .replace(/\d/g, '')
                .trim()
        },
    },
})
