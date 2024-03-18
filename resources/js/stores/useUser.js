import { useLocalStorage, useSessionStorage, StorageSerializers } from '@vueuse/core'
import { clear as clearCart } from './useCart'
import { computed, watch } from 'vue'

export const token = useLocalStorage('token', '')
const userStorage = useSessionStorage('user', {}, { serializer: StorageSerializers.object })
let isRefreshing = false

export const refresh = async function () {
    if (!token.value) {
        clearStorage()
        return false
    }

    if (isRefreshing) {
        console.debug('Refresh canceled, request already in progress...')
        return
    }

    try {
        isRefreshing = true
        window.magentoUser.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
        let response = await magentoUser.get('customers/me').finally(() => {
            isRefreshing = false
        })
        if(!token.value) {
            clearStorage()
            return false
        }

        userStorage.value = response.data
    } catch (error) {
        if (error.response.status == 401) {
            clearStorage()
            return false
        }

        console.error(error)
        return false
    }

    return true
}

export const clear = async function () {
    clearStorage()
    await clearCart()
}

const clearStorage = function () {
    token.value = ''
    userStorage.value = {}
    
    let addressTypes = ['billing_address', 'shipping_address']
    addressTypes.forEach(varName => {
        if (window.app.checkout?.[varName]?.customer_id || window.app.checkout?.[varName]?.customer_address_id) {
            window.app.checkout[varName] = window.address_defaults
        }
    });
}

export const user = computed({
    get() {
        if (token.value && !userStorage.value?.id) {
            refresh()
        }

        return userStorage.value
    },
    set(value) {
        userStorage.value = value
    },
})

// If token gets changed or emptied we should update the user.
watch(token, refresh)

export default () => user
