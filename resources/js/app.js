import Vue from 'vue'
window._ = require('lodash')
window.axios = require('axios')
window.debug = process.env.MIX_DEBUG == 'true'
window.Vue = Vue
window.Turbolinks = require('turbolinks')
window.Notify = (message, type) => {
    window.app.$emit('notification-message', message, type);
}
Turbolinks.start()
import TurbolinksAdapter from 'vue-turbolinks'
Vue.use(TurbolinksAdapter)

import ReactiveSearch from '@appbaseio/reactivesearch-vue'
Vue.use(ReactiveSearch)

import AsyncComputed from 'vue-async-computed'
Vue.use(AsyncComputed)

import { directive as onClickaway } from 'vue-clickaway';
Vue.directive('on-click-away', onClickaway);

require('./axios')
require('./filters')
require('./mixins')
require('./turbolinks')

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('cart', require('./components/Cart/Cart.vue').default)
Vue.component('listing', require('./components/Listing/Listing.vue').default)
Vue.component('query-filter', require('./components/Listing/Filters/QueryFilter.vue').default)
Vue.component('category-filter', require('./components/Listing/Filters/CategoryFilter.vue').default)
Vue.component('category-filter-category', require('./components/Listing/Filters/CategoryFilterCategory.vue').default)
Vue.component('checkout', require('./components/Checkout/Checkout.vue').default)
Vue.component('checkout-success', require('./components/Checkout/CheckoutSuccess.vue').default)
Vue.component('login', require('./components/Checkout/Login.vue').default)
Vue.component('coupon', require('./components/Coupon/Coupon.vue').default)
Vue.component('toggler', require('./components/Elements/Toggler.vue').default)
Vue.component('slider', require('./components/Elements/Slider.vue').default)
Vue.component('add-to-cart', require('./components/Product/AddToCart.vue').default)
Vue.component('images', require('./components/Product/Images.vue').default)
Vue.component('user', require('./components/User/User.vue').default)
Vue.component('graphql', require('./components/Graphql.vue').default)
Vue.component('notifications', require('./components/Notifications/Notifications.vue').default)
Vue.component('notification', require('./components/Notifications/Notification.vue').default)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

document.addEventListener('turbolinks:load', () => {
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
            checkout: {
                step: 1,
                shipping_address: {
                    'firstname': localStorage.shipping_firstname ?? (window.debug ? 'Bruce' : ''),
                    'lastname': localStorage.shipping_lastname ?? (window.debug ? 'Wayne' : ''),
                    'postcode': localStorage.shipping_postcode ?? (window.debug ? '72000' : ''),
                    'street': localStorage.shipping_street?.split(',') ?? (window.debug ? ['Mountain Drive', 1007, ''] : ['', '', '']),
                    'city': localStorage.shipping_city ?? (window.debug ? 'Gotham' : ''),
                    'telephone': localStorage.shipping_telephone ?? (window.debug ? '530-7972' : ''),
                    'country_id': localStorage.shipping_country_id ?? (window.debug ? 'TR' : ''),
                },
                billing_address: {
                    'firstname': localStorage.billing_firstname ?? (window.debug ? 'Bruce' : ''),
                    'lastname': localStorage.billing_lastname ?? (window.debug ? 'Wayne' : ''),
                    'postcode': localStorage.billing_postcode ?? (window.debug ? '72000' : ''),
                    'street': localStorage.billing_street?.split(',') ?? (window.debug ? ['Mountain Drive', 1007, ''] : ['', '', '']),
                    'city': localStorage.billing_city ?? (window.debug ? 'Gotham' : ''),
                    'telephone': localStorage.billing_telephone ?? (window.debug ? '530-7972' : ''),
                    'country_id': localStorage.billing_country_id ?? (window.debug ? 'TR' : ''),
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
                    Turbolinks.visit('/search?q=' + value)
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
})
