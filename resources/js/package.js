window.debug = import.meta.env.VITE_DEBUG == 'true'
window.Notify = (message, type, params = [], link = null) => window.app.$emit('notification-message', message, type, params, link)
if (!window.process) {
    // Workaround for process missing, if data is actually needed from here you should apply the following polyfill.
    // https://stackoverflow.com/questions/72221740/how-do-i-polyfill-the-process-node-module-in-the-vite-dev-server
    window.process = {}
}

import './polyfills'
import { useLocalStorage, StorageSerializers, useScrollLock } from '@vueuse/core'
import useOrder from './stores/useOrder.js'
import useCart from './stores/useCart'
import useUser from './stores/useUser'
import useMask from './stores/useMask'
import { swatches, clear as clearSwatches } from './stores/useSwatches'
import { clear as clearAttributes } from './stores/useAttributes.js'
import './vue'
import './fetch'
import './filters'
import './mixins'
import './cookies'
import './callbacks'
import './vue-components'
import { fetchCount } from './stores/useFetches.js'
;(() => import('./turbolinks'))()

if (import.meta.env.VITE_DEBUG === 'true') {
    document.addEventListener('vue:loaded', () => {
        window.app.$on('notification-message', function (message, type, params, link) {
            switch (type) {
                case 'error':
                    console.error(...arguments)
                    break
                case 'warning':
                    console.warn(...arguments)
                    break
                case 'success':
                case 'info':
                default:
                    console.log(...arguments)
            }
        })
    })
}

document.addEventListener('vue:loaded', () => {
    const lastStoreCode = useLocalStorage('last_store_code', window.config.store_code)
    if (lastStoreCode.value !== window.config.store_code) {
        clearAttributes()
        clearSwatches()
        lastStoreCode.value = window.config.store_code
    }
})

let booting = false
function init() {
    if (booting || document.body.contains(window.app.$el)) {
        return
    }
    booting = true

    // https://vuejs.org/api/application.html#app-config-performance
    Vue.config.performance = import.meta.env.VITE_PERFORMANCE == 'true'
    Vue.prototype.window = window
    Vue.prototype.config = window.config

    // Check if the localstorage needs a flush.
    let cachekey = useLocalStorage('cachekey')
    if (cachekey.value !== window.config.cachekey) {
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

    requestAnimationFrame(() => {
        window.app = new Vue({
            el: '#app',
            data: {
                custom: {},
                config: window.config,
                loadingCount: fetchCount,
                loading: false,
                loadAutocomplete: false,
                csrfToken: document.querySelector('[name=csrf-token]')?.content,
                cart: useCart(),
                order: useOrder(),
                user: useUser(),
                mask: useMask(),
                showTax: window.config.show_tax,
                swatches: swatches,
                scrollLock: useScrollLock(document.body),
            },
            methods: {
                search(value) {
                    if (value.length) {
                        Turbo.visit(window.url('/search?q=' + encodeURIComponent(value)))
                    }
                },

                setSearchParams(url) {
                    window.history.pushState(window.history.state, '', new URL(url))
                },

                toggleScroll(bool = null) {
                    if (bool === null) {
                        this.scrollLock = !this.scrollLock
                    } else {
                        this.scrollLock = bool
                    }
                },

                resizedPath(imagePath, size, store = null) {
                    if (!store) {
                        store = window.config.store
                    }

                    let url = new URL(imagePath)
                    url = url.pathname.replace('/media', '')

                    return `/storage/${store}/resizes/${size}/magento${url}`
                },
            },
            computed: {
                // Wrap the local storage in getter and setter functions so you do not have to interact using .value
                guestEmail: wrapValue(
                    useLocalStorage('email', window.debug ? 'wayne@enterprises.com' : '', { serializer: StorageSerializers.string }),
                ),

                loggedIn() {
                    return this.user?.is_logged_in
                },

                hasCart() {
                    return this.cart?.id && this.cart.items.length
                },

                canOrder() {
                    return this.cart.items.every((item) => item.is_available)
                },

                queryParams() {
                    return new URLSearchParams(window.location.search)
                },
            },
            watch: {
                loadingCount: function (count) {
                    window.app.$data.loading = count > 0
                },
            },
            mounted() {
                setTimeout(() => {
                    const event = new CustomEvent('vue:mounted', { detail: { vue: window.app } })
                    document.dispatchEvent(event)
                })
            },
            // Chromium handles task scheduling differently than Firefox, so we need to use a different event for the destroy event.
            destroyEvent: !!window.chrome ? 'turbo:before-cache-timeout' : 'turbo:before-cache',
        })

        setTimeout(() => {
            booting = false
            const event = new CustomEvent('vue:loaded', { detail: { vue: window.app } })
            document.dispatchEvent(event)
        })
    })
}

document.addEventListener('turbo:load', init)
document.addEventListener('turbo:before-cache', () =>
    setTimeout(() => document.dispatchEvent(new CustomEvent('turbo:before-cache-timeout'))),
)
setTimeout(init)
