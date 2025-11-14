import { directive as onClickaway } from 'vue3-click-away'

document.addEventListener('vue:loaded', function (event) {
    event.detail.vue.directive('on-click-away', onClickaway)
    event.detail.vue.directive('blur', (el) => {
        el.removeAttribute('v-blur')
    })
    event.detail.vue.directive('txt', {
        mounted(el, binding) {
            el.textContent = binding.value == null ? '' : String(binding.value)
        },
        updated(el, binding) {
            if (binding.value !== binding.oldValue) {
                el.textContent = binding.value == null ? '' : String(binding.value)
            }
        },
    })

    event.detail.vue.directive('htm', {
        mounted(el, binding) {
            el.innerHTML = binding.value == null ? '' : String(binding.value)
        },
        updated(el, binding) {
            if (binding.value !== binding.oldValue) {
                el.innerHTML = binding.value == null ? '' : String(binding.value)
            }
        },
    })

    event.detail.vue.config.compilerOptions.isCustomElement = (tag) => /^turbo-.+/.test(tag)
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
