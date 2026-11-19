import { useLocalStorage, StorageSerializers } from '@vueuse/core'
import { clear as clearCart, fetchCustomerCart, linkUserToCart } from './useCart'
import { computed, watch } from 'vue'
import Jwt from '../jwt'
import { mask } from './useMask'

export const token = useLocalStorage('token', '')
const userStorage = useLocalStorage('user', {}, { serializer: StorageSerializers.object })
let refreshPromise = null

export const refresh = async function () {
    if (!token.value) {
        userStorage.value = {}
        return false
    }

    // Wait for the request that's already in progress instead of
    // canceling, so callers always continue with the loaded user.
    if (refreshPromise) {
        return refreshPromise
    }

    if (Jwt.isJwt(token.value) && Jwt.decode(token.value)?.isExpired()) {
        Notify(window.config.translations.errors.session_expired, 'error')
        await clear()

        return false
    }

    refreshPromise = (async () => {
        try {
            // TODO: Migrate to GraphQL?
            userStorage.value = (await window.magentoAPI('GET', 'customers/me', {}, { redirectOnExpiration: false })) || {}
        } catch (error) {
            if (error instanceof SessionExpired) {
                await clear()
            } else {
                throw error
            }
        } finally {
            refreshPromise = null
        }
    })()

    return refreshPromise
}

export const clear = async function () {
    token.value = ''
    userStorage.value = {}
    await clearCart()
}

export const login = async function (email, password) {
    return magentoGraphQL(
        'mutation generateCustomerToken ($email: String!, $password: String!) { generateCustomerToken (email: $email, password: $password) { token } }',
        {
            email: email,
            password: password,
        },
    ).then(async (response) => {
        await loginByToken(response.data.generateCustomerToken.token)

        return response
    })
}

export const loginByToken = async function (customerToken) {
    token.value = customerToken

    if (mask.value) {
        await linkUserToCart()
    } else {
        await fetchCustomerCart()
    }

    window.app.$emit('logged-in')
}

export const logout = async function () {
    await magentoGraphQL('mutation { revokeCustomerToken { result } }', {}, { notifyOnError: false, redirectOnExpiration: false }).finally(
        async () => await clear(),
    )
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

// Update the user on page load so the user will get logged out immediately
// if their session has expired.
window.setTimeout(() => refresh(), 200)

export default () => user
