import { mask } from './stores/useMask'
import { computed } from 'vue'

document.addEventListener('vue:loaded', function (event) {
    event.detail.vue.config.globalProperties.asyncForEach = async (array, callback) => {
        for (let index = 0; index < array.length; index++) {
            await callback(array[index], index, array)
        }
    }

    event.detail.vue.config.globalProperties.magentoCart = async (method, endpoint, data) => {
        if (window.app.config.globalProperties.loggedIn.value) {
            return await window.magentoAPI(method, 'carts/mine/' + endpoint, data)
        } else {
            return await window.magentoAPI(method, 'guest-carts/' + mask.value + '/' + endpoint, data)
        }
    }

    event.detail.vue.config.globalProperties.currencySymbolLocation = computed(() => {
        return new Intl.NumberFormat(config.locale.replace('_', '-'), {
            style: 'currency',
            currency: config.currency,
        }).formatToParts(1)?.[0]?.type === 'currency'
            ? 'left'
            : 'right'
    })

    event.detail.vue.config.globalProperties.currencySymbol = computed(() => {
        return new Intl.NumberFormat(config.locale.replace('_', '-'), {
            style: 'currency',
            currency: config.currency,
            maximumFractionDigits: 0,
        })
            .format(0)
            .replace(/\d/g, '')
            .trim()
    })
})
