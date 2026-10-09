import { useEventListener } from '@vueuse/core'

function isLoading(el) {
    return el.getAttribute('aria-disabled') === 'true'
}

function setLoading(el, loading) {
    loading ? el.setAttribute('aria-disabled', 'true') : el.removeAttribute('aria-disabled')
    el.loadingValue = el.value
}

function prevent(event) {
    if (!isLoading(event.currentTarget)) {
        return
    }

    event.preventDefault()
}

function revert(event) {
    if (!isLoading(event.currentTarget)) {
        return
    }

    event.stopImmediatePropagation()
    event.currentTarget.value = event.currentTarget.loadingValue
}

export default {
    mounted(el, binding) {
        useEventListener(el, ['beforeinput', 'click'], prevent)
        useEventListener(el, ['input', 'change'], revert, { capture: true })

        setLoading(el, binding.value)
    },

    updated(el, binding) {
        setLoading(el, binding.value)
    },
}
