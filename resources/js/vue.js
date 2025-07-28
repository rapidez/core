import { directive as onClickaway } from 'vue3-click-away'

document.addEventListener('vue:loaded', function (event) {
    event.detail.vue.directive('on-click-away', onClickaway)
    event.detail.vue.directive('blur', (el) => {
        el.removeAttribute('v-blur')
    })
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
