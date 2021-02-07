export default {
    methods: {
        async getUser() {
            if (this.$root.user === null) {
                if (localStorage.token) {
                    if (!localStorage.user) {
                        await this.refreshUser()
                    }
                    this.$root.user = JSON.parse(localStorage.user)
                }
            }
            return this.$root.user
        },

        async refreshUser(redirect = false) {
            try {
                let response = await magentoUser.get('customers/me')

                localStorage.user = JSON.stringify(response.data)
            } catch (error) {
                if (error.response.status == 401) {
                    localStorage.removeItem('token')
                }
                if (redirect) {
                    Turbolinks.visit('/login')
                }
            }
        },

        logout(redirect = '/') {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            localStorage.removeItem('mask')
            localStorage.removeItem('cart')
            this.$root.user = null
            Turbolinks.visit(redirect)
        }
    },

    asyncComputed: {
        user: function () {
            return this.getUser()
        }
    }
}
