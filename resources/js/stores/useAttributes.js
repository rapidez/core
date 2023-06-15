import { computedAsync, useLocalStorage } from '@vueuse/core'

export const attributesStorage = useLocalStorage('attributes', {})
let isRefreshing = false

export const refresh = async function () {
    if (isRefreshing) {
        console.debug('Refresh canceled, request already in progress...')
        return
    }

    try {
        isRefreshing = true
        var response = await axios.get('/api/attributes').finally(() => {
            isRefreshing = false
        })
    } catch (error) {
        console.error(error)
        Notify(window.config.translations.errors.wrong, 'error')

        return false
    }

    if (response === undefined || !response.data) {
        return false
    }

    attributesStorage.value = response.data

    return true
}

export const clear = async function () {
    attributesStorage.value = {}
}

export const attributes = computedAsync(
    async () => {
        if (Object.keys(attributesStorage.value).length === 0) {
            await refresh()
        }

        return attributesStorage
    },
    attributesStorage.value,
    { lazy: true, shallow: false }
)

export default () => attributes
