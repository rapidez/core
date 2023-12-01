Vue.mixin({
    methods: {
        async asyncForEach(array, callback) {
            for (let index = 0; index < array.length; index++) {
                await callback(array[index], index, array)
            }
        },

        async magentoCart(method, endpoint, data) {
            if (this.$root.loggedIn) {
                return await window.magentoAPI(method, 'carts/mine/' + endpoint, data)
            } else {
                return await window.magentoAPI(method, 'guest-carts/' + localStorage.mask + '/' + endpoint, data)
            }
        },
    },
})
