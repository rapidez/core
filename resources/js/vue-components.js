import TurbolinksAdapter from 'vue-turbolinks'
import toggler from './components/Elements/Toggler.vue'
import addToCart from './components/Product/AddToCart.vue'
import user from './components/User/User.vue'
import slider from './components/Elements/Slider.vue'
import lazy from './components/Lazy.vue'
import graphql from './components/Graphql.vue'
import graphqlMutation from './components/GraphqlMutation.vue'
import recursion from './components/Recursion.vue'
import notifications from './components/Notifications/Notifications.vue'
import globalSlideover from './components/GlobalSlideover.vue'
import globalSlideoverInstance from './components/GlobalSlideoverInstance.vue'
import VueTurboFrame from './components/VueTurboFrame.vue'
import images from './components/Product/Images.vue'
import quantitySelect from './components/Product/QuantitySelect.vue'
import { defineAsyncComponent, defineComponent } from 'vue'

document.addEventListener('vue:loaded', function (event) {
    const vue = event.detail.vue
    vue.use(TurbolinksAdapter)

    vue.component('toggler', toggler)

    vue.component('add-to-cart', addToCart)

    vue.component('user', user)

    vue.component('slider', slider)
    vue.component('lazy', lazy)
    vue.component('graphql', graphql)
    vue.component('graphql-mutation', graphqlMutation)
    vue.component('recursion', recursion)

    vue.component('notifications', notifications)
    vue.component('global-slideover', globalSlideover)
    vue.component('global-slideover-instance', globalSlideoverInstance)

    vue.component('vue-turbo-frame', VueTurboFrame)
    vue.component('images', images)

    vue.component('quantity-select', quantitySelect)

    vue.component(
        'autocomplete',
        defineAsyncComponent({
            // https://vuejs.org/guide/components/async#async-components
            loader: () =>
                new Promise(function (resolve, reject) {
                    document.addEventListener('loadAutoComplete', () => import('./components/Search/Autocomplete.vue').then(resolve))
                }),
            // https://vuejs.org/guide/components/async#loading-and-error-states
            loadingComponent: defineComponent({
                data: () => ({
                    loaded: false,
                    searchClient: null,
                    searchHistory: {},
                }),

                render() {
                    // TODO: seems broken, replaced by <Suspense> (https://github.com/vuejs/core/pull/13997)
                    return this.$slots.default(this)
                },
                delay: 0,
            }),
        }),
    )
    if (import.meta.env.VITE_DISABLE_DOUBLE_CLICK_FIX !== 'true') {
        document.addEventListener('vue:loaded', function () {
            // Workaround double click bug on ipad & iphone: https://stackoverflow.com/questions/71535540/keyboard-doesnt-open-in-ios-on-focus
            if (
                ['iPad Simulator', 'iPhone Simulator', 'iPod Simulator', 'iPad', 'iPhone', 'iPod'].includes(navigator.platform) ||
                (navigator.userAgent.includes('Mac') && 'ontouchend' in document)
            ) {
                // Simply load the autocomplete ~600ms after pageload so it won't impact pageload but will
                // Be fast enough that most users won't notice
                setTimeout(() => window.document.dispatchEvent(new window.Event('loadAutoComplete')), 600)
            }
        })
    }
    vue.component(
        'checkout-login',
        defineAsyncComponent(() => import('./components/Checkout/CheckoutLogin.vue')),
    )
    vue.component(
        'login',
        defineAsyncComponent(() => import('./components/User/Login.vue')),
    )
    vue.component(
        'listing',
        defineAsyncComponent(() => import('./components/Listing/Listing.vue')),
    )
    vue.component(
        'search-suggestions',
        defineAsyncComponent(() => import('./components/Listing/SearchSuggestions.vue')),
    )
    vue.component(
        'checkout-success',
        defineAsyncComponent(() => import('./components/Checkout/CheckoutSuccess.vue')),
    )
    vue.component(
        'popup',
        defineAsyncComponent(() => import('./components/Popup.vue')),
    )
    vue.component(
        'range-slider',
        defineAsyncComponent(() => import('./components/Elements/RangeSlider.vue')),
    )
    vue.component(
        'recently-viewed',
        defineAsyncComponent(() => import('./components/RecentlyViewed.vue')),
    )
})
