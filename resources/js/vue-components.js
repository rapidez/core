import Teleport from 'vue2-teleport'
Vue.component('teleport', Teleport)

import toggler from './components/Elements/Toggler.vue'
Vue.component('toggler', toggler)

import addToCart from './components/Product/AddToCart.vue'
Vue.component('add-to-cart', addToCart)

import user from './components/User/User.vue'
Vue.component('user', user)

import slider from './components/Elements/Slider.vue'
Vue.component('slider', slider)
import lazy from './components/Lazy.vue'
Vue.component('lazy', lazy)
import graphql from './components/Graphql.vue'
Vue.component('graphql', graphql)
import graphqlMutation from './components/GraphqlMutation.vue'
Vue.component('graphql-mutation', graphqlMutation)
import recursion from './components/Recursion.vue'
Vue.component('recursion', recursion)

import notifications from './components/Notifications/Notifications.vue'
Vue.component('notifications', notifications)
import globalSlideover from './components/GlobalSlideover.vue'
Vue.component('global-slideover', globalSlideover)
import globalSlideoverInstance from './components/GlobalSlideoverInstance.vue'
Vue.component('global-slideover-instance', globalSlideoverInstance)

import images from './components/Product/Images.vue'
Vue.component('images', images)

import quantitySelect from './components/Product/QuantitySelect.vue'
Vue.component('quantity-select', quantitySelect)

Vue.component('autocomplete', () => ({
    // https://v2.vuejs.org/v2/guide/components-dynamic-async.html#Async-Components
    component: new Promise(function (resolve, reject) {
        document.addEventListener('loadAutoComplete', () => import('./components/Search/Autocomplete.vue').then(resolve))
    }),
    // https://v2.vuejs.org/v2/guide/components-dynamic-async.html#Handling-Loading-State
    loading: {
        data: () => ({
            loaded: false,
            searchClient: null,
        }),

        render() {
            return this.$scopedSlots.default(this)
        },
    },
    delay: 0,
}))
Vue.component('checkout-login', () => import('./components/Checkout/CheckoutLogin.vue'))
Vue.component('login', () => import('./components/User/Login.vue'))
Vue.component('listing', () => import('./components/Listing/Listing.vue'))
Vue.component('search-suggestions', () => import('./components/Listing/SearchSuggestions.vue'))
Vue.component('checkout-success', () => import('./components/Checkout/CheckoutSuccess.vue'))
Vue.component('popup', () => import('./components/Popup.vue'))
Vue.component('range-slider', () => import('./components/Elements/RangeSlider.vue'))
