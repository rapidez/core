import Vue from 'vue'
window.Vue = Vue

import { directive as onClickaway } from 'vue-clickaway'
Vue.directive('on-click-away', onClickaway)
Vue.directive('v-blur', (el) => {
    el.removeAttribute('v-blur')
})

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
