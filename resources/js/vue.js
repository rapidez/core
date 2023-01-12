import Vue from 'vue'
window.Vue = Vue
import AsyncComputed from 'vue-async-computed'
Vue.use(AsyncComputed)

import { directive as onClickaway } from 'vue-clickaway'
Vue.directive('on-click-away', onClickaway)

/**
 * add getter/setter for ref with value.
 * For use in computed.
 *
 * @param {Ref} storage
 * @returns mixed
 */
window.wrapValue = function(storage)
{
    return {
        get: function() {
            return storage.value;
        },
        set: function(value) {
            storage.value = value;
        }
    }
}
