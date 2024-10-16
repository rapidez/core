import { useLocalStorage, useSessionStorage, StorageSerializers } from '@vueuse/core'
import { useCookies } from '@vueuse/integrations/useCookies'
import { clear as clearCart, fetchCustomerCart, linkUserToCart } from './useCart'
import { clear as clearOrder } from './useOrder'
import { computed, watch } from 'vue'
import Jwt from '../jwt'
import { mask } from './useMask'

/**
 * @deprecated using localstorage to retrieve the token is deprecated, use the useUser.token instead
 */
const localstorageToken = useLocalStorage('token', '')
const { get: getCookie, set: setCookie } = useCookies(['token'])

export const token = computed({
    get() {
        const token = getCookie('token') ?? ''
        localstorageToken.value = token

        return token
    },
    set(value) {
        let options = {
            path: '/',
            secure: window.location.protocol === 'https:',
            maxAge: 31556952,
            sameSite: 'strict',
        }

        if (Jwt.isJwt(value)) {
            delete options['maxAge']
            options.expires = Jwt.decode(value).expDate
        }

        setCookie('token', value, options)
        localstorageToken.value = value
    },
})

const userStorage = useSessionStorage('user', {}, { serializer: StorageSerializers.object })
let currentRefresh = null

export const refresh = async function () {
    if (!token.value) {
        userStorage.value = {}
        return false
    }

    if (currentRefresh) {
        console.debug('Refresh canceled, request already in progress...')
        return currentRefresh;
    }

    if (Jwt.isJwt(token.value) && Jwt.decode(token.value)?.isExpired()) {
        Notify(window.config.translations.errors.session_expired, 'error')
        await clear()

        return false
    }

    return currentRefresh = (async function() {
        try {
            userStorage.value = (await window.magentoGraphQL(`{ customer { ${config.queries.customer} } }`))?.data?.customer
        } catch (error) {
            if (error instanceof SessionExpired) {
                await clear()
            } else {
                throw error
            }

            return false;
        }

        return true;
    })().finally(() => {currentRefresh = null})
}

export const clear = async function () {
    token.value = ''
    userStorage.value = {}
    await clearCart()
    await clearOrder()
}

export const isEmailAvailable = async function (email) {
    if (!email) {
        return true
    }

    // As of Commerce 2.4.7, by default the query always returns a value of true for all email addresses.
    // You can change this behavior by setting the Stores > Configuration > Sales > Checkout > Enable Guest Checkout Login field in the Admin to Yes.
    return await magentoGraphQL('query isEmailAvailable ($email:String!) { isEmailAvailable (email: $email) { is_email_available } }', {
        email: email,
    })
        .then((response) => response.data.isEmailAvailable.is_email_available)
        .catch(() => true)
}

export const register = async function (email, firstname, lastname, password, input = {}) {
    return magentoGraphQL('mutation register ($input:CustomerCreateInput!) { createCustomerV2 (input: $input) { customer { email } } }', {
        input: {
            email: email,
            firstname: firstname,
            lastname: lastname,
            password: password,
            ...input,
        },
    }).then(async (response) => {
        if (response.data?.createCustomerV2?.customer?.email) {
            window.app.$emit('registered', {
                email: email,
                firstname: firstname,
                lastname: lastname,
                ...input,
            })
            await login(email, password)
        }

        return response
    })
}

export const login = async function (email, password) {
    return (
        magentoGraphQL(
            'mutation generateCustomerToken ($email: String!, $password: String!) { generateCustomerToken (email: $email, password: $password) { token } }',
            {
                email: email,
                password: password,
            },
        )
            // Set Auth Token
            .then(async (response) => {
                token.value = response.data.generateCustomerToken.token

                return response
            })
            // Link or Fetch Cart
            .then(async (response) => {
                if (mask.value) {
                    await linkUserToCart()
                } else {
                    await fetchCustomerCart()
                }
                return response
            })
            // Fire logged in event
            .then(async (response) => {
                window.app.$emit('logged-in')
                return response
            })
    )
}

export const logout = async function () {
    await magentoGraphQL('mutation { revokeCustomerToken { result } }', {}, { notifyOnError: false, redirectOnExpiration: false }).finally(
        async () => {
            await clear()
            window.app.$emit('logged-out')
        },
    )
}

export const user = computed({
    get() {
        if (token.value && !userStorage.value?.email) {
            refresh()
        }

        userStorage.value.is_logged_in = Boolean(userStorage.value?.email)

        return userStorage.value
    },
    set(value) {
        userStorage.value = value
    },
})

// If token gets changed or emptied we should update the user.
watch(token, refresh)

document.addEventListener('vue:loaded', function (event) {
    event.detail.vue.$on('logout', async function (data = {}) {
        await logout()
        useLocalStorage('email', '').value = ''
        Turbo.cache.clear()

        if (data?.redirect) {
            this.$nextTick(() => (window.location.href = window.url(data?.redirect)))
        }
    })
})

export default () => user
