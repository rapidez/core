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

Vue.component('autocomplete', () => import('./components/Search/Autocomplete.vue'))
Vue.component('checkout-login', () => import('./components/Checkout/CheckoutLogin.vue'))
Vue.component('login', () => import('./components/User/Login.vue'))
Vue.component('listing', () => import('./components/Listing/Listing.vue'))
Vue.component('checkout-success', () => import('./components/Checkout/CheckoutSuccess.vue'))
Vue.component('popup', () => import('./components/Popup.vue'))
Vue.component('selected-filters-values', () => import('./components/Listing/Filters/SelectedFiltersValues.vue'))
Vue.component('range-slider', () => import('./components/Elements/RangeSlider.vue'))

Vue.component('ais-instant-search', () => import('vue-instantsearch/vue2/es/src/components/InstantSearch'))
Vue.component('ais-search-box', () => import('vue-instantsearch/vue2/es/src/components/SearchBox.vue.js'))
Vue.component('ais-hits', () => import('vue-instantsearch/vue2/es/src/components/Hits.js'))
Vue.component('ais-index', () => import('vue-instantsearch/vue2/es/src/components/Index.js'))
Vue.component('ais-configure', () => import('vue-instantsearch/vue2/es/src/components/Configure.js'))
Vue.component('ais-highlight', () => import('vue-instantsearch/vue2/es/src/components/Highlight.vue.js'))

Vue.component('ais-refinement-list', () => import('vue-instantsearch/vue2/es/src/components/RefinementList.vue.js'))
Vue.component('ais-hierarchical-menu', () => import('vue-instantsearch/vue2/es/src/components/HierarchicalMenu.vue.js'))
Vue.component('ais-range-input', () => import('vue-instantsearch/vue2/es/src/components/RangeInput.vue.js'))
Vue.component('ais-current-refinements', () => import('vue-instantsearch/vue2/es/src/components/CurrentRefinements.vue.js'))
Vue.component('ais-clear-refinements', () => import('vue-instantsearch/vue2/es/src/components/ClearRefinements.vue.js'))
Vue.component('ais-hits-per-page', () => import('vue-instantsearch/vue2/es/src/components/HitsPerPage.vue.js'))
Vue.component('ais-sort-by', () => import('vue-instantsearch/vue2/es/src/components/SortBy.vue.js'))
Vue.component('ais-pagination', () => import('vue-instantsearch/vue2/es/src/components/Pagination.vue.js'))
Vue.component('ais-stats', () => import('vue-instantsearch/vue2/es/src/components/Stats.vue.js'))
