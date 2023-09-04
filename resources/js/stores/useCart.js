import { useSessionStorage, StorageSerializers, useLocalStorage } from '@vueuse/core'
import { computed, watch } from 'vue'
import { mask, clear as clearMask } from './useMask'
import { token } from './useUser'

const cartStorage = useSessionStorage('cart', {}, { serializer: StorageSerializers.object })
let hasRefreshed = false
let isRefreshing = false

export const refresh = async function () {
    hasRefreshed = true
    if (!mask.value && !token.value) {
        cartStorage.value = {}
        return false
    }

    if (isRefreshing) {
        console.debug('Refresh canceled, request already in progress...')
        return
    }

    try {
        isRefreshing = true
        let response = await axios.get(window.url('/api/cart/' + (token.value ? token.value : mask.value))).finally(() => {
            isRefreshing = false
        })
        cartStorage.value = !mask.value && !token.value ? {} : response.data
        window.app.$emit('cart-refreshed')
    } catch (error) {
        if (error.response.status == 404) {
            mask.value = null
            return false
        }

        Notify(window.config.translations.errors.wrong, 'warning')
        console.error(error)

        return false
    }
}

export const clear = async function () {
    await clearAddresses()
    await clearMask()
    await refresh()
}

export const clearAddresses = async function () {
    useLocalStorage('billing_address').value = null
    useLocalStorage('shipping_address').value = null
}

export const cart = computed({
    get() {
        if (!cartStorage.value?.entity_id && mask.value) {
            refresh()
        }
        return cartStorage.value
    },
    set(value) {
        cartStorage.value = value
    },
})

// If mask gets added, updated or removed we should update the cart.
watch(mask, refresh)
// refresh the cart on first pageload after a while
window.setTimeout(() => window.requestIdleCallback(() => !hasRefreshed && refresh), 5000)

export default () => cart
