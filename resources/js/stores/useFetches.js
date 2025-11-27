import { computed, ref } from 'vue'
import { until } from '@vueuse/core'

export let fetches = ref([])

export const fetchCount = computed(() => {
    return fetches.value.length
})

export async function addFetch(promise) {
    fetches.value.push(promise)

    promise.finally((...args) => {
        removeFetch(promise)

        return args
    })

    return promise
}

export async function removeFetch(promise) {
    fetches.value = fetches.value.filter((fetch) => fetch !== promise)

    return fetches
}

export async function allSettled(options = null) {
    await until(fetchCount).toBe(0, options)
}
