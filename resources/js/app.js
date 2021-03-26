import Vue from 'vue'
window._ = require('lodash')
window.axios = require('axios')
window.debug = process.env.MIX_DEBUG == 'true'
window.Vue = Vue
window.Turbolinks = require('turbolinks')
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
Vue.component('category', require('./components/Category/Category.vue').default)
Vue.component('category-filter', require('./components/Category/Filters/CategoryFilter.vue').default)
Vue.component('swatch-filter', require('./components/Category/Filters/SwatchFilter.vue').default)
Vue.component('checkout', require('./components/Checkout/Checkout.vue').default)
Vue.component('login', require('./components/Checkout/Login.vue').default)
Vue.component('coupon', require('./components/Coupon/Coupon.vue').default)
Vue.component('toggler', require('./components/Elements/Toggler.vue').default)
Vue.component('add-to-cart', require('./components/Product/AddToCart.vue').default)
Vue.component('images', require('./components/Product/Images.vue').default)
Vue.component('user', require('./components/User/User.vue').default)
Vue.component('graphql', require('./components/Graphql.vue').default)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

document.addEventListener('turbolinks:load', () => {
    Vue.prototype.config = window.config
    window.app = new Vue({
        el: '#app',
        data: {
            config: window.config,
            loading: false,
            guestEmail: null,
            user: null,
            cart: null,
            checkout: {
                step: 1,
                shipping_address: {
                    'firstname': window.debug ? 'Bruce' : null,
                    'lastname': window.debug ? 'Wayne' : null,
                    'postcode': window.debug ? '72000' : null,
                    'street': window.debug ? ['Mountain Drive', 1007] : [],
                    'city': window.debug ? 'Gotham' : null,
                    'telephone': window.debug ? '530-7972' : null,
                    'country_id': window.debug ? 'TR' : null,
                },
                billing_address: {
                    'street': [],
                },
                hide_billing: true,

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
                Turbolinks.visit('/search?q=' + value)
            }
        },
        watch: {
            'checkout.step': function (step) {
                history.replaceState(null, null, '#'+config.checkout_steps[step])
            }
        }
    })
})
