window.debug = import.meta.env.VITE_DEBUG == 'true'
window.Notify = (message, type, params = [], link = null) => window.app.$emit('notification-message', message, type, params, link);
if (!window.process) {
    // Workaround for process missing, if data is actually needed from here you should apply the following polyfill.
    // https://stackoverflow.com/questions/72221740/how-do-i-polyfill-the-process-node-module-in-the-vite-dev-server
    window.process = {};
}

import './lodash'
import './vue'
import { computed } from 'vue';
import './axios'
import './filters'
import './mixins'
import './turbolinks'
import './cookies'
import './callbacks'

import cart from './components/Cart/Cart.vue'
Vue.component('cart', cart)
import toggler from './components/Elements/Toggler.vue'
Vue.component('toggler', toggler)
import slider from './components/Elements/Slider.vue'
Vue.component('slider', slider)
import addToCart from './components/Product/AddToCart.vue'
Vue.component('add-to-cart', addToCart)
import user from './components/User/User.vue'
Vue.component('user', user)
import lazy from './components/Lazy.vue'
Vue.component('lazy', lazy)
import graphql from './components/Graphql.vue'
Vue.component('graphql', graphql)
import graphqlMutation from './components/GraphqlMutation.vue'
Vue.component('graphql-mutation', graphqlMutation)
import notifications from './components/Notifications/Notifications.vue'
Vue.component('notifications', notifications)
import notification from './components/Notifications/Notification.vue'
Vue.component('notification', notification)
import images from './components/Product/Images.vue'
Vue.component('images', images)

import categoryFilter from './components/Listing/Filters/CategoryFilter.vue'
Vue.component('category-filter', categoryFilter)
import categoryFilterCategory from './components/Listing/Filters/CategoryFilterCategory.vue'
Vue.component('category-filter-category', categoryFilterCategory)

Vue.component('autocomplete', () => import('./components/Search/Autocomplete.vue'))
Vue.component('login', () => import('./components/Checkout/Login.vue'))
Vue.component('listing', () => import('./components/Listing/Listing.vue'))
Vue.component('coupon', () => import('./components/Coupon/Coupon.vue'))
Vue.component('checkout', () => import('./components/Checkout/Checkout.vue'))
Vue.component('checkout-success', () => import('./components/Checkout/CheckoutSuccess.vue'))

function init() {
    Vue.prototype.window = window
    Vue.prototype.config = window.config

    // Check if the localstorage needs a flush.
    if (localStorage.cachekey !== window.config.cachekey) {
        window.config.flushable_localstorage_keys.forEach((key) => {
            localStorage.removeItem(key)
        })
        localStorage.cachekey = window.config.cachekey
    }

    window.app = new Vue({
        el: '#app',
        data: {
            custom: {},
            config: window.config,
            loadingCount: 0,
            loading: computed(() => window.app?.$data?.loadingCount > 0),
            guestEmail: localStorage.email ?? null,
            user: null,
            cart: null,
            loadAutocomplete: false,
            checkout: {
                step: 1,
                shipping_address: {
                    'customer_address_id': null,
                    'firstname': localStorage.shipping_firstname ?? (window.debug ? 'Bruce' : ''),
                    'lastname': localStorage.shipping_lastname ?? (window.debug ? 'Wayne' : ''),
                    'postcode': localStorage.shipping_postcode ?? (window.debug ? '72000' : ''),
                    'street': localStorage.shipping_street?.split(',') ?? (window.debug ? ['Mountain Drive', 1007, ''] : ['', '', '']),
                    'city': localStorage.shipping_city ?? (window.debug ? 'Gotham' : ''),
                    'telephone': localStorage.shipping_telephone ?? (window.debug ? '530-7972' : ''),
                    'country_id': localStorage.shipping_country_id ?? (window.debug ? 'NL' : window.config.default_country),
                    'custom_attributes': [],
                },
                billing_address: {
                    'customer_address_id': null,
                    'firstname': localStorage.billing_firstname ?? (window.debug ? 'Bruce' : ''),
                    'lastname': localStorage.billing_lastname ?? (window.debug ? 'Wayne' : ''),
                    'postcode': localStorage.billing_postcode ?? (window.debug ? '72000' : ''),
                    'street': localStorage.billing_street?.split(',') ?? (window.debug ? ['Mountain Drive', 1007, ''] : ['', '', '']),
                    'city': localStorage.billing_city ?? (window.debug ? 'Gotham' : ''),
                    'telephone': localStorage.billing_telephone ?? (window.debug ? '530-7972' : ''),
                    'country_id': localStorage.billing_country_id ?? (window.debug ? 'NL' : window.config.default_country),
                    'custom_attributes': [],
                },
                hide_billing: localStorage.hide_billing === 'false' ? false : true,

                totals: {},

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
        asyncComputed: {
            swatches () {
                if (localStorage.swatches) {
                    return JSON.parse(localStorage.swatches);
                }

                return axios.get('/api/swatches')
                    .then((response) => {
                        localStorage.swatches = JSON.stringify(response.data)
                        return response.data
                    })
                    .catch((error) => {
                        Notify(window.config.errors.wrong, 'error')
                    })
            }
        }
    })

    if(window.debug) {
        window.app.$on('notification-message', console.debug);
    }
}

document.addEventListener('turbo:load', init)
