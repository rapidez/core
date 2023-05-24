window.debug = import.meta.env.VITE_DEBUG == 'true'
window.Notify = (message, type, params = [], link = null) => window.app.$emit('notification-message', message, type, params, link);
if (!window.process) {
    // Workaround for process missing, if data is actually needed from here you should apply the following polyfill.
    // https://stackoverflow.com/questions/72221740/how-do-i-polyfill-the-process-node-module-in-the-vite-dev-server
    window.process = {};
}

import { useLocalStorage, StorageSerializers } from '@vueuse/core'
import useCart from './stores/useCart';
import useUser from './stores/useUser';
import useSwatches from './stores/useSwatches';
import './vue'
import { computed } from 'vue';
import './axios'
import './filters'
import './mixins'
import './turbolinks'
import './cookies'
import './callbacks'
import './vue-components'

function init() {
    // https://vuejs.org/api/application.html#app-config-performance
    Vue.config.performance = import.meta.env.VITE_PERFORMANCE == 'true'
    Vue.prototype.window = window
    Vue.prototype.config = window.config

    let swatches = useLocalStorage('swatches', {});

    // Check if the localstorage needs a flush.
    let cachekey = useLocalStorage('cachekey');
    if (cachekey.value !== window.config.cachekey) {
        window.config.flushable_localstorage_keys.forEach((key) => {
            useLocalStorage(key).value = null;
        })

        cachekey.value = window.config.cachekey
    }

    window.address_defaults = {
        'customer_address_id': null,
        'firstname': (window.debug ? 'Bruce' : ''),
        'lastname': (window.debug ? 'Wayne' : ''),
        'postcode': (window.debug ? '72000' : ''),
        'street': (window.debug ? ['Mountain Drive', 1007, ''] : ['', '', '']),
        'city': (window.debug ? 'Gotham' : ''),
        'telephone': (window.debug ? '530-7972' : ''),
        'country_id': (window.debug ? 'NL' : window.config.default_country),
        'custom_attributes': [],
    }

    window.app = new Vue({
        el: '#app',
        data: {
            custom: {},
            config: window.config,
            loadingCount: 0,
            loading: computed(() => window.app?.$data?.loadingCount > 0),
            loadAutocomplete: false,
            cart: useCart(),
            user: useUser(),
            swatches: useSwatches(),
            checkout: {
                step: 1,
                totals: {},

                shipping_address: useLocalStorage('shipping_address', address_defaults, {mergeDefaults: true, serializer: StorageSerializers.object}),
                billing_address: useLocalStorage('billing_address', address_defaults, {mergeDefaults: true, serializer: StorageSerializers.object}),
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
                    Turbo.visit('/search?q=' + encodeURIComponent(value))
                }
            },
            setSearchParams(url) {
                window.history.pushState(window.history.state, '', new URL(url))
            }
        },
        computed: {
            // Wrap the local storage in getter and setter functions so you do not have to interact using .value
            guestEmail: wrapValue(useLocalStorage('email', (window.debug ? 'wayne@enterprises.com' : ''), {serializer: StorageSerializers.string})),
        },
    })

    if(window.debug) {
        window.app.$on('notification-message', console.debug);
    }
}

document.addEventListener('turbo:load', init)
