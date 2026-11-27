import { pushNotification } from './stores/useNotifications'

window.debug = import.meta.env.VITE_DEBUG == 'true'
window.Notify = (message, type = 'info', params = [], link = null) => {
    if (!message) {
        message = window.config?.translations?.errors?.wrong ?? null
        reportError(new Error(`Notify called without a message (type: ${type})`))

        if (!message || type !== 'error') {
            return
        }
    }

    pushNotification({
        message: message,
        type: type,
        params: params,
        link: link,
        timestamp: +new Date(),
    })
}

if (!window.process) {
    // Workaround for process missing, if data is actually needed from here you should apply the following polyfill.
    // https://stackoverflow.com/questions/72221740/how-do-i-polyfill-the-process-node-module-in-the-vite-dev-server
    window.process = {}
}

import './polyfills'
import { useLocalStorage } from '@vueuse/core'
import './vue'
import './fetch'
import './helpers'
import './mixins'
import './cookies'
import './callbacks'
import './vue-components'
import './instantsearch'
import { fetchCount } from './stores/useFetches'
import { createRapidezApp } from './root'
import { createApp, watch } from 'vue'

if (!import.meta.env.SSR) {
    ;(() => import('./turbolinks'))()
}

if (import.meta.env.VITE_DEBUG === 'true') {
    window.$on(
        'notification-message',
        function (message, type, params, link) {
            switch (type) {
                case 'error':
                    console.error(message, type, params, link)
                    break
                case 'warning':
                    console.warn(message, type, params, link)
                    break
                case 'success':
                case 'info':
                default:
                    console.log(message, type, params, link)
            }
        },
        { autoRemove: false },
    )
}

let booting = false
let rootEl = null
async function init() {
    if (booting || (rootEl && document.body.contains(rootEl))) {
        return
    }
    booting = true
    for (let i = 0; i < 20; i++) {
        // Wait until config is available, or has thrown an error, for a max of 1s
        if (window.config.store || window.configError) {
            break
        }
        await new Promise((resolve) => setTimeout(resolve, 50))
    }
    rootEl = document.querySelector('#app')

    if (!rootEl || rootEl.__vue_app__ || rootEl.__rapidez__) {
        return
    }
    rootEl.__rapidez__ = true

    // Check if the localstorage needs a flush.
    let cachekey = useLocalStorage('cachekey')
    if (window.config.cachekey && cachekey.value !== window.config.cachekey) {
        window.config.flushable_localstorage_keys.forEach((key) => {
            useLocalStorage(key).value = null
        })

        cachekey.value = window.config.cachekey
    }

    let address = window.debug ? ['Mountain Drive', '1007', ''] : ['', '', '']

    for (let i = address.length; i >= window.config.street_lines; i--) {
        address[i - 1] = (address[i - 1] + ' ' + address.pop()).trim()
    }

    window.address_defaults = {
        customer_address_id: null,
        same_as_shipping: true,
        firstname: window.debug ? 'Bruce' : '',
        lastname: window.debug ? 'Wayne' : '',
        postcode: window.debug ? '72000' : '',
        street: address,
        city: window.debug ? 'Gotham' : '',
        telephone: window.debug ? '530-7972' : '',
        country_code: window.debug ? 'NL' : window.config.default_country,
        custom_attributes: [],
    }

    // Heartbeat every 15 minutes to keep CSRF alive
    if (!window.heartbeat) {
        window.heartbeat = setInterval(
            () => {
                rapidezFetch('/heartbeat')
            },
            1000 * 60 * 15,
        )
    }

    requestAnimationFrame(() => {
        createRapidezApp(createApp, {
            el: '#app',
            mounted() {
                window.$on('configError', () => {
                    app.config.globalProperties.configError.value = true
                    throw new Error('Config.js failed to load because of an error.')
                })
                if (window.configError ?? false) {
                    window.$emit('configError')
                }

                window.app.config.globalProperties.refs = this.$refs
                setTimeout(() => {
                    const event = new CustomEvent('vue:mounted', { detail: { vue: window.app, rootNode: this } })
                    document.dispatchEvent(event)
                })
            },
            // If we have view transitions, we need to make sure we destroy after render.
            destroyEvent: !!document.startViewTransition ? 'turbo:before-cache-timeout' : 'turbo:before-cache',
        })

        watch(fetchCount, function (count) {
            app.config.globalProperties.loading.value = count > 0
        })

        setTimeout(() => {
            booting = false
            const event = new CustomEvent('vue:loaded', { detail: { vue: window.app } })
            document.dispatchEvent(event)

            window.app.mount('#app')
        })
    })
}

if (!import.meta.env.SSR) {
    document.addEventListener('turbo:load', init)
    document.addEventListener('turbo:before-cache', () =>
        setTimeout(() => document.dispatchEvent(new CustomEvent('turbo:before-cache-timeout'))),
    )
    setTimeout(init)
}
