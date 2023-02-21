(() => {
    const components = {
        ...import.meta.glob(['./components/*.vue', '!./components/*.lazy.vue'], { eager: true, import: 'default' }),
        ...import.meta.glob(['./components/*.lazy.vue'], { eager: false, import: 'default' })
    };
    for (const path in components) {
        Vue.component(path.split('/').pop().split('.').shift(), components[path])
    }
})();

import toggler from './components/Elements/Toggler.vue'
Vue.component('toggler', toggler)

import cart from './components/Cart/Cart.vue'
Vue.component('cart', cart)
import addToCart from './components/Product/AddToCart.vue'
Vue.component('add-to-cart', addToCart)

import user from './components/User/User.vue'
Vue.component('user', user)

import slider from './components/Elements/Slider.vue'
Vue.component('slider', slider)

import notifications from './components/Notifications/Notifications.vue'
Vue.component('notifications', notifications)

import images from './components/Product/Images.vue'
Vue.component('images', images)

Vue.component('autocomplete', () => import('./components/Search/Autocomplete.vue'))
Vue.component('login', () => import('./components/Checkout/Login.vue'))
Vue.component('listing', () => import('./components/Listing/Listing.vue'))
Vue.component('coupon', () => import('./components/Coupon/Coupon.vue'))
Vue.component('checkout', () => import('./components/Checkout/Checkout.vue'))
Vue.component('checkout-success', () => import('./components/Checkout/CheckoutSuccess.vue'))


