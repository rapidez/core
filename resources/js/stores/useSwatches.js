// TODO: In this file there is a lot of duplication compared to useAttributes. Can we improve that?
import { computedAsync, useLocalStorage } from '@vueuse/core'

export const swatchesStorage = useLocalStorage('swatches', {})
let isRefreshing = false
let hasFetched = false

export const refresh = async function () {
    if (isRefreshing) {
        console.debug('Refresh canceled, request already in progress...')
        return
    }

    try {
        isRefreshing = true
        swatchesStorage.value = await window.rapidezAPI('get', 'swatches') || {}
        isRefreshing = false
        hasFetched = true
    } catch (error) {
        isRefreshing = false
        console.error(error)
        Notify(window.config.translations.errors.wrong, 'error')
    }
}

export const clear = async function () {
    swatchesStorage.value = {}
    hasFetched = false
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
