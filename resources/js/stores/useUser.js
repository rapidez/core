import { useSessionStorage, StorageSerializers } from '@vueuse/core'
import { clear as clearCart } from './useCart'
import { computed, watch } from 'vue'
import { useCookies } from '@vueuse/integrations/useCookies'
import Jwt from '../jwt'

const cookies = useCookies(['customer_token'])

export const token = computed({
    get() {
        return cookies.get('customer_token')
    },
    set(value) {
        if (value === null || value === undefined || value === '') {
            cookies.remove('customer_token')
        }

        cookies.set('customer_token', value)
    },
})
const userStorage = useSessionStorage('user', {}, { serializer: StorageSerializers.object })
let isRefreshing = false

export const refresh = async function () {
    if (!token.value) {
        userStorage.value = {}
        return false
    }

    if (Jwt.isJwt(token.value) && Jwt.decode(token.value)?.isExpired()) {
        console.debug('Token has expired')
        clear()
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
        userStorage.value = !token.value ? {} : response.data
    } catch (error) {
        if (error.response.status == 401) {
            token.value = ''
            return false
        }

        console.error(error)
        return false
    }

    return true
}

export const clear = async function () {
    token.value = ''
    userStorage.value = {}
    await clearCart()
}

export const user = computed({
    get() {
        if (!token.value && userStorage.value?.id) {
            // Token has been removed externally
            clear()
        }

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
