import { StorageSerializers, useLocalStorage } from '@vueuse/core'
import { computed, watch } from 'vue'
import { mask, clear as clearMask } from './useMask'
import { token } from './useUser'

const cartStorage = useLocalStorage('cart', {}, { serializer: StorageSerializers.object })

export const refresh = async function () {
    if (!mask.value && !token.value) {
        cartStorage.value = {}
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
        if (!cartStorage.value?.id && mask.value) {
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

export default () => cart
