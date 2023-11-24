import { computedAsync, useLocalStorage } from '@vueuse/core'

export const swatchesStorage = useLocalStorage('swatches', {})
let isRefreshing = false
let hasFetched = false;

export const refresh = async function () {
    if (isRefreshing) {
        console.debug('Refresh canceled, request already in progress...')
        return
    }

    try {
        isRefreshing = true
        var response = await axios.get(window.url('/api/swatches')).finally(() => {
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

    hasFetched = true
    swatchesStorage.value = response.data

    return true
}

export const clear = async function () {
    swatchesStorage.value = {}
    hasFetched = false;
}

export const swatches = computedAsync(
    async () => {
        if (!hasFetched && Object.keys(swatchesStorage.value).length === 0) {
            await refresh()
        }

        return swatchesStorage
    },
    swatchesStorage.value,
    { lazy: true, shallow: false },
)

export default () => swatches
