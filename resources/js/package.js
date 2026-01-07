window.debug = import.meta.env.VITE_DEBUG == 'true'
window.Notify = (message, type = 'info', params = [], link = null) =>
    setTimeout(() => window.$emit('notification-message', message, type, params, link), window.app ? 0 : 500)

if (!window.process) {
    // Workaround for process missing, if data is actually needed from here you should apply the following polyfill.
    // https://stackoverflow.com/questions/72221740/how-do-i-polyfill-the-process-node-module-in-the-vite-dev-server
    window.process = {}
}

import './polyfills'
import { useLocalStorage, StorageSerializers, useScrollLock } from '@vueuse/core'
import useOrder from './stores/useOrder.js'
import { cart } from './stores/useCart'
import { user } from './stores/useUser'
import useMask from './stores/useMask'
import './vue'
import './fetch'
import './helpers'
import './mixins'
import './cookies'
import './callbacks'
import './vue-components'
import './instantsearch'
import { fetchCount } from './stores/useFetches.js'
import { computed, createApp, watch } from 'vue'
;(() => import('./turbolinks'))()

if (import.meta.env.VITE_DEBUG === 'true') {
    window.$on(
        'rapidez:notification-message',
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
        // Wait until config is available, for a max of 1s
        if (window.config.store) {
            break
        }
        await new Promise((resolve) => setTimeout(resolve, 50))
    }
    rootEl = document.querySelector('#app')

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
        window.app = createApp({
            el: '#app',
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

                resizedPath(imagePath, size, store = null, sku = false) {
                    if (!store) {
                        store = window.config.store
                    }

                    if (sku) {
                        return window.url(`/storage/${store}/resizes/${size}/sku/${imagePath}.webp`)
                    }

                    let url = new URL(imagePath)
                    url = url.pathname.replace('/media', '')

                    return window.url(`/storage/${store}/resizes/${size}/magento${url}`)
                },

                categoryPositions(categoryId) {
                    return {
                        function_score: {
                            script_score: {
                                script: {
                                    source: `doc.containsKey('positions.${categoryId}') && !doc['positions.${categoryId}'].empty && doc['positions.${categoryId}'].value =~ /^\\d+$/ ? Integer.parseInt(doc['positions.${categoryId}'].value) : 0`,
                                },
                            },
                        },
                    }
                },
            },
            mounted() {
                window.app.config.globalProperties.refs = this.$refs
                setTimeout(() => {
                    const event = new CustomEvent('vue:mounted', { detail: { vue: window.app, rootNode: this } })
                    document.dispatchEvent(event)
                })
            },
            // If we have view transitions, we need to make sure we destroy after render.
            destroyEvent: !!document.startViewTransition ? 'turbo:before-cache-timeout' : 'turbo:before-cache',
        })
        // https://vuejs.org/api/application.html#app-config-performance
        window.app.config.performance = import.meta.env.VITE_PERFORMANCE == 'true'
        window.app.config.globalProperties = {
            custom: {},
            config: window.config,
            refs: {},
            loadingCount: fetchCount,
            loading: false,
            autocompleteFacadeQuery: '',
            csrfToken: document.querySelector('[name=csrf-token]')?.content,
            cart: cart,
            order: useOrder(),
            user: user,
            mask: useMask(),
            showTax: window.config.show_tax,
            scrollLock: useScrollLock(document.body),
            // Wrap the local storage in getter and setter functions so you do not have to interact using .value
            guestEmail: wrapValue(
                useLocalStorage('email', window.debug ? 'wayne@enterprises.com' : '', { serializer: StorageSerializers.string }),
            ),

            loggedIn: computed(function () {
                return user.value?.is_logged_in
            }),

            hasCart: computed(function () {
                return cart.value?.id && cart.value.items.length
            }),

            canOrder: computed(function () {
                return cart.value.items.every((item) => item.is_available)
            }),
        }
        window.app.config.globalProperties.window = window
        window.app.config.globalProperties.config = window.config

        watch(fetchCount, function (count) {
            app.config.globalProperties.loading = count > 0
        })

        setTimeout(() => {
            booting = false
            const event = new CustomEvent('vue:loaded', { detail: { vue: window.app } })
            document.dispatchEvent(event)

            window.app.mount('#app')
        })
    })
}

document.addEventListener('turbo:load', init)
document.addEventListener('turbo:before-cache', () =>
    setTimeout(() => document.dispatchEvent(new CustomEvent('turbo:before-cache-timeout'))),
)
setTimeout(init)
