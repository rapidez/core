import { useLocalStorage } from '@vueuse/core'
import { useCookies } from '@vueuse/integrations/useCookies'
import { computed } from 'vue'
import { fetchCart } from './useCart'

/**
 * @deprecated using localstorage to retrieve the mask is deprecated, use the useMask.mask instead
 */
const localstorageMask = useLocalStorage('mask', '')
const { get: getCookie, set: setCookie } = useCookies(['mask'])

export const mask = computed({
    get() {
        const mask = getCookie('mask') ?? ''
        localstorageMask.value = mask

        return mask
    },
    set(value) {
        let options = {
            path: '/',
            secure: window.location.protocol === 'https:',
            maxAge: 31556952,
            sameSite: 'strict',
        }

        setCookie('mask', value, options)
        localstorageMask.value = value
    },
})

export const refreshMask = async function () {
    try {
        await fetchCart()
        // FetchCart automatically fills the mask.
    } catch (error) {
        Notify(window.config.translations.errors.wrong, 'error')
        console.error(error)
    }
}

export const clearMask = async function () {
    mask.value = ''
}

export default () => mask
