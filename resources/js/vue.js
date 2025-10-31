import Vue from 'vue'
window.Vue = Vue

import { directive as onClickaway } from 'vue-clickaway'
import { vIntersectionObserver } from '@vueuse/components'
Vue.directive('on-click-away', onClickaway)
Vue.directive('intersection-observer', vIntersectionObserver)
Vue.directive('blur', (el) => {
    el.removeAttribute('v-blur')
})

Vue.config.ignoredElements = [/^turbo-.+/]

/**
 * add getter/setter for ref with value.
 * For use in computed.
 *
 * @param {Ref} storage
 * @returns mixed
 */
window.wrapValue = function (storage) {
    return {
        get: function () {
            return storage.value
        },
        set: function (value) {
            storage.value = value
        },
    }
}
