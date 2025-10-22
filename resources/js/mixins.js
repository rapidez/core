import { mask } from './stores/useMask'

document.addEventListener('vue:loaded', function (event) {
    event.detail.vue.mixin({
        methods: {
            async asyncForEach(array, callback) {
                for (let index = 0; index < array.length; index++) {
                    await callback(array[index], index, array)
                }
            },

            async magentoCart(method, endpoint, data) {
                if (this.$root.config.globalProperties.loggedIn.value) {
                    return await window.magentoAPI(method, 'carts/mine/' + endpoint, data)
                } else {
                    return await window.magentoAPI(method, 'guest-carts/' + mask.value + '/' + endpoint, data)
                }
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
})
