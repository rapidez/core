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
import images from './components/Product/Images.vue'
import quantitySelect from './components/Product/QuantitySelect.vue'
import { defineAsyncComponent, defineComponent } from 'vue'

document.addEventListener('vue:loaded', function (event) {
    const vue = event.detail.vue;
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

    vue.component('images', images)

    vue.component('quantity-select', quantitySelect)

    vue.component('autocomplete', defineAsyncComponent({
        // https://v2.vuejs.org/v2/guide/components-dynamic-async.html#Async-Components
        loader: () => new Promise(function (resolve, reject) {
            document.addEventListener('loadAutoComplete', () => import('./components/Search/Autocomplete.vue').then(resolve))
        }),
        // https://v2.vuejs.org/v2/guide/components-dynamic-async.html#Handling-Loading-State
        loadingComponent: {
            data: () => ({
                loaded: false,
                searchClient: null,
                searchHistory: {},
            }),

            render() {
                // TODO: seems broken, replaced by <Suspense>
                return this.$slots.default(this)
            },
        },
        delay: 0,
    }))
    vue.component('checkout-login', defineAsyncComponent(() => import('./components/Checkout/CheckoutLogin.vue')))
    vue.component('login', defineAsyncComponent(() => import('./components/User/Login.vue')))
    vue.component('listing', defineAsyncComponent(() => import('./components/Listing/Listing.vue')))
    vue.component('search-suggestions', defineAsyncComponent(() => import('./components/Listing/SearchSuggestions.vue')))
    vue.component('checkout-success', defineAsyncComponent(() => import('./components/Checkout/CheckoutSuccess.vue')))
    vue.component('popup', defineAsyncComponent(() => import('./components/Popup.vue')))
    vue.component('range-slider', defineAsyncComponent(() => import('./components/Elements/RangeSlider.vue')))
})
