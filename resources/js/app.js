window.axios = require('axios')
window.debug = process.env.MIX_DEBUG == 'true'
window.Notify = (message, type, params = []) => window.app.$emit('notification-message', message, type, params);

require('./lodash')
require('./vue')
require('./axios')
require('./filters')
require('./mixins')
require('./turbolinks')
require('./webworker')
require('./cookies')

Vue.component('cart', require('./components/Cart/Cart.vue').default)
Vue.component('toggler', require('./components/Elements/Toggler.vue').default)
Vue.component('slider', require('./components/Elements/Slider.vue').default)
Vue.component('add-to-cart', require('./components/Product/AddToCart.vue').default)
Vue.component('user', require('./components/User/User.vue').default)
Vue.component('lazy', require('./components/Lazy.vue').default)
Vue.component('graphql', require('./components/Graphql.vue').default)
Vue.component('graphql-mutation', require('./components/GraphqlMutation.vue').default)
Vue.component('notifications', require('./components/Notifications/Notifications.vue').default)
Vue.component('notification', require('./components/Notifications/Notification.vue').default)
Vue.component('images', require('./components/Product/Images.vue').default)

Vue.component('category-filter', require('./components/Listing/Filters/CategoryFilter.vue').default)
Vue.component('category-filter-category', require('./components/Listing/Filters/CategoryFilterCategory.vue').default)

Vue.component('autocomplete', () => import(/* webpackChunkName: "autocomplete" */ './components/Search/Autocomplete.vue'))
Vue.component('login', () => import(/* webpackChunkName: "account" */ './components/Checkout/Login.vue'))
Vue.component('listing', () => import(/* webpackChunkName: "listing" */ './components/Listing/Listing.vue'))
Vue.component('coupon', () => import(/* webpackChunkName: "cart" */ './components/Coupon/Coupon.vue'))
Vue.component('checkout', () => import(/* webpackChunkName: "checkout" */ './components/Checkout/Checkout.vue'))
Vue.component('checkout-success', () => import(/* webpackChunkName: "checkout" */ './components/Checkout/CheckoutSuccess.vue'))

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
            loading: false,
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
                    Turbolinks.visit('/search?q=' + encodeURIComponent(value))
                }
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
}

document.addEventListener('turbolinks:load', init)
