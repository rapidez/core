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

import notifications from './components/Notifications/Notifications.vue'
Vue.component('notifications', notifications)
import globalSlideover from './components/GlobalSlideover.vue'
Vue.component('global-slideover', globalSlideover)

import images from './components/Product/Images.vue'
Vue.component('images', images)

Vue.component('autocomplete', () => import('./components/Search/Autocomplete.vue'))
Vue.component('login', () => import('./components/Checkout/Login.vue'))
Vue.component('listing', () => import('./components/Listing/Listing.vue'))
Vue.component('checkout', () => import('./components/Checkout/Checkout.vue'))
Vue.component('checkout-success', () => import('./components/Checkout/CheckoutSuccess.vue'))
Vue.component('popup', () => import('./components/Popup.vue'))
