import { useEventListener } from '@vueuse/core'

// Replace old window.app.$emit and window.app.$on
export const emit = (window.$emit = (event, ...args) => {
    return document.dispatchEvent(new CustomEvent(event, { detail: { args: args } }))
})

/**
 *
 * @param {string} event
 * @param {callback} callback
 * @param {object} options
 * {
 *      autoremove: true, // use autoRemove when used within Vue to automatically remove it on unmount (page navigation)
 *      defer: true, // Defer execution of your callback to the next "event cycle"
 * }
 */
export const on = (window.$on = (event, callback, options = {}) => {
    options = {
        ...{ autoRemove: true, defer: true },
        ...options,
    }

    const callbackFn = (eventData) => {
        if (options.defer) {
            setTimeout(() => {
                callback(...eventData.detail.args)
            })
            return
        }
        callback(...eventData.detail.args)
    }

    if (options.autoRemove) {
        useEventListener(document, event, callbackFn)
    } else {
        document.addEventListener(event, callbackFn)
    }
})
