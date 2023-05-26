Vue.mixin({
    computed: {
        loggedIn() {
            return Boolean(this.$root.user?.id)
        }
    }
})


