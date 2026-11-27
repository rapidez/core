import { useLocalStorage, StorageSerializers, useScrollLock } from '@vueuse/core'
import useOrder from './stores/useOrder'
import { cart } from './stores/useCart'
import { user } from './stores/useUser'
import useMask from './stores/useMask'
import { fetchCount } from './stores/useFetches'
import { computed, ref } from 'vue'

export function createRapidezApp(create, root = {}) {
    window.app = create({
        ...root,
        methods: {
            search(value) {
                if (value.length) {
                    Turbo.visit(window.url('/search?q=' + encodeURIComponent(value)))
                }
            },

            setSearchParams(url) {
                window.history.pushState(window.history.state, '', new URL(url))
            },

            toggleScroll(bool = null) {
                if (bool === null) {
                    this.scrollLock = !this.scrollLock
                } else {
                    this.scrollLock = bool
                }
            },

            resizedPath(imagePath, size, store = null, sku = false) {
                if (!store) {
                    store = window.config.store
                }

                if (sku) {
                    return window.url(`/storage/${store}/resizes/${size}/sku/${imagePath}.webp`)
                }

                let url = new URL(imagePath)
                url = url.pathname.replace('/media', '').replace('/.renditions/', '/')

                return window.url(`/storage/${store}/resizes/${size}/magento${url}`)
            },

            categoryPositions(categoryId) {
                return {
                    function_score: {
                        script_score: {
                            script: {
                                source: `doc.containsKey('positions.${categoryId}') && !doc['positions.${categoryId}'].empty && doc['positions.${categoryId}'].value =~ /^\\d+$/ ? Integer.parseInt(doc['positions.${categoryId}'].value) : 0`,
                            },
                        },
                    },
                }
            },
            ...root.methods,
        },
    })

    // https://vuejs.org/api/application.html#app-config-performance
    window.app.config.performance = import.meta.env.VITE_PERFORMANCE == 'true'
    window.app.config.globalProperties = {
        custom: ref({}),
        config: window.config,
        refs: ref({}),
        loadingCount: fetchCount,
        loading: ref(false),
        autocompleteFacadeQuery: '',
        csrfToken: ref(document.querySelector('[name=csrf-token]')?.content),
        cart: cart,
        order: useOrder(),
        user: user,
        mask: useMask(),
        showTax: window.config.show_tax,
        scrollLock: useScrollLock(document.body),
        configError: ref(false),
        // Wrap the local storage in getter and setter functions so you do not have to interact using .value
        guestEmail: wrapValue(
            useLocalStorage('email', window.debug ? 'wayne@enterprises.com' : '', { serializer: StorageSerializers.string }),
        ),

        loggedIn: computed(function () {
            return user.value?.is_logged_in
        }),

        hasCart: computed(function () {
            return cart.value?.id && cart.value.items.length
        }),

        canOrder: computed(function () {
            return cart.value.items.every((item) => item.is_available && !item.errors?.length)
        }),
    }
    window.app.config.globalProperties.window = window
    window.app.config.globalProperties.config = window.config

    return window.app
}
