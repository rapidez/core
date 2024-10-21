window.debug = import.meta.env.VITE_DEBUG == 'true'
window.Notify = (message, type, params = [], link = null) => window.app.$emit('notification-message', message, type, params, link)
if (!window.process) {
    // Workaround for process missing, if data is actually needed from here you should apply the following polyfill.
    // https://stackoverflow.com/questions/72221740/how-do-i-polyfill-the-process-node-module-in-the-vite-dev-server
    window.process = {}
}

import './polyfills'
import { useLocalStorage, StorageSerializers, useScrollLock } from '@vueuse/core'
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

;(() => import('./turbolinks'))()

function init() {
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

    window.address_defaults = {
        customer_address_id: null,
        firstname: window.debug ? 'Bruce' : '',
        lastname: window.debug ? 'Wayne' : '',
        postcode: window.debug ? '72000' : '',
        street: window.debug ? ['Mountain Drive', 1007, ''] : ['', '', ''],
        city: window.debug ? 'Gotham' : '',
        telephone: window.debug ? '530-7972' : '',
        country_id: window.debug ? 'NL' : window.config.default_country,
        custom_attributes: [],
    }

    window.app = new Vue({
        el: '#app',
        data: {
            custom: {},
            config: window.config,
            loadingCount: 0,
            loading: false,
            loadAutocomplete: false,
            csrfToken: document.querySelector('[name=csrf-token]').content,
            cart: useCart(),
            user: useUser(),
            mask: useMask(),
            showTax: window.config.show_tax,
            swatches: swatches,
            scrollLock: useScrollLock(document.body),
            checkout: {
                step: 1,
                totals: {},

                shipping_address: useLocalStorage('shipping_address', address_defaults, {
                    mergeDefaults: true,
                    serializer: StorageSerializers.object,
                }),
                billing_address: useLocalStorage('billing_address', address_defaults, {
                    mergeDefaults: true,
                    serializer: StorageSerializers.object,
                }),
                hide_billing: useLocalStorage('hide_billing', true),

                shipping_method: null,
                shipping_methods: [],

                payment_method: null,
                payment_methods: [],

                agreement_ids: [],

                // This can be used to prevent the checkout from going
                // to the next step which is useful in combination
                // with the "CheckoutPaymentSaved" event to
                // implement payment providers.
                doNotGoToTheNextStep: false,
            },
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
        },
        computed: {
            // Wrap the local storage in getter and setter functions so you do not have to interact using .value
            guestEmail: wrapValue(
                useLocalStorage('email', window.debug ? 'wayne@enterprises.com' : '', { serializer: StorageSerializers.string }),
            ),

            loggedIn() {
                return Boolean(this.user?.id)
            },

            hasCart() {
                return this.cart?.id && this.cart.items.length
            },
        },
        watch: {
            loadingCount: function (count) {
                window.app.$data.loading = count > 0
            },
        },
    })

    const lastStoreCode = useLocalStorage('last_store_code', window.config.store_code)
    if (lastStoreCode.value !== window.config.store_code) {
        clearAttributes()
        clearSwatches()
        lastStoreCode.value = window.config.store_code
    }

    if (window.debug) {
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
    }

    const event = new CustomEvent('vue:loaded')
    document.dispatchEvent(event)
}
document.addEventListener('turbo:load', init)
