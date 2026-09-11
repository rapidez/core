import { pushNotification } from './stores/useNotifications'

window.debug = import.meta.env.VITE_DEBUG == 'true'
window.Notify = (message, type = 'info', params = [], link = null) => {
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
import { useLocalStorage, StorageSerializers, useScrollLock } from '@vueuse/core'
import useOrder from './stores/useOrder'
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
import { instantsearchComponents } from './instantsearch'
import { fetchCount } from './stores/useFetches'
import { computed, createApp, ref, watch } from 'vue'
;(() => import('./turbolinks'))()

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

// Booting is deferred to the next frame so the server rendered markup can paint
// before Vue starts. requestAnimationFrame never fires while the tab is hidden,
// which would leave the page unbooted until it's focused, so a timer runs against
// it and whichever comes first wins.
function nextFrame(callback) {
    let called = false
    let run = () => {
        if (called) {
            return
        }
        called = true
        callback()
    }

    requestAnimationFrame(run)
    setTimeout(run, 50)
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

    nextFrame(async () => {
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
                    url = url.pathname.replace('/media', '').replace('/.renditions/', '/')

                    return window.url(`/storage/${store}/resizes/${size}/magento${url}`)
                },

                categoryPositions(categoryId) {
                    // The category is passed as a param instead of being interpolated into the
                    // source, otherwise every category compiles its own script; Elasticsearch
                    // caches compiled scripts by source and rate limits compilations.
                    let field = `'positions.' + params.category_id`

                    return {
                        function_score: {
                            script_score: {
                                script: {
                                    source: `doc.containsKey(${field}) && !doc[${field}].empty && doc[${field}].value =~ /^\\d+$/ ? Integer.parseInt(doc[${field}].value) : 0`,
                                    params: { category_id: String(categoryId) },
                                },
                            },
                        },
                    }
                },
            },
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

        // https://vuejs.org/api/application.html#app-config-performance
        window.app.config.performance = import.meta.env.VITE_PERFORMANCE == 'true'
        window.app.config.globalProperties = {
            custom: ref({}),
            config: window.config,
            refs: ref({}),
            loadingCount: fetchCount,
            loading: ref(false),
            autocompleteFacadeQuery: '',
            csrfToken: ref(document.querySelector('[name=csrf-token]')?.content),
            cart: cart,
            order: useOrder(),
            user: user,
            mask: useMask(),
            showTax: window.config.show_tax,
            scrollLock: useScrollLock(document.body),
            configError: ref(false),
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
            app.config.globalProperties.loading.value = count > 0
        })

        // The listing renders through the InstantSearch components, so it's worth
        // waiting for that (preloaded) chunk here; they then register synchronously
        // instead of resolving one by one during the first renders.
        if (document.querySelector('listing')) {
            await instantsearchComponents.catch(() => {})
        }

        booting = false
        const event = new CustomEvent('vue:loaded', { detail: { vue: window.app } })
        document.dispatchEvent(event)

        window.app.mount('#app')
    })
}

document.addEventListener('turbo:load', init)
document.addEventListener('turbo:before-cache', () =>
    setTimeout(() => document.dispatchEvent(new CustomEvent('turbo:before-cache-timeout'))),
)
setTimeout(init)
