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

        async refreshUser(redirect = true) {
            if (!localStorage.token) {
                localStorage.removeItem('user')
                this.$root.user = null
                return
            }

            try {
                let response = await magentoUser.get('customers/me')
                localStorage.user = JSON.stringify(response.data)
                window.app.user = response.data
            } catch (error) {
                if (error.response.status == 401) {
                    this.logout(redirect ? '/login' : false)
                }
            }
        },

        async login(username, password, loginCallback = false) {
            await magento
                .post('integration/customer/token', {
                    username: username,
                    password: password,
                })
                .then(async (response) => {
                    localStorage.token = response.data
                    window.magentoUser.defaults.headers.common['Authorization'] = `Bearer ${localStorage.token}`

                    await this.refreshUser(false)

                    this.setCheckoutCredentialsFromDefaultUserAddresses()
                    await window.app.$emit('logged-in')
                    if (loginCallback) {
                        await loginCallback()
                    }
                })
                .catch((error) => {
                    Notify(error.response.data.message, 'error', error.response.data?.parameters)
                    return false
                })
        },

        logout(redirect = false) {
            this.$root.$emit('logout', { redirect: redirect })
        },

        onLogout(data = {}) {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            localStorage.removeItem('email')
            this.$root.user = null
            Turbo.cache.clear()

            if (data?.redirect) {
                window.location.href = data?.redirect
            }
        },

        async createCustomer(customer) {
            try {
                let response = await magentoUser.post('customers', {
                    customer: {
                        email: customer.email,
                        firstname: customer.firstname,
                        lastname: customer.lastname,
                        store_id: window.config.store,
                    },
                    password: customer.password,
                })
                return response.data
            } catch (error) {
                Notify(error.response.data.message, 'error', error.response.data?.parameters)
                return false
            }
        },

        setCheckoutCredentialsFromDefaultUserAddresses() {
            if (this.$root && this.$root.user) {
                this.setCustomerAddressByAddressId('shipping', this.$root.user.default_shipping)
                this.setCustomerAddressByAddressId('billing', this.$root.user.default_billing)
            }
        },

        setCustomerAddressByAddressId(type, id) {
            if (!id) {
                return
            }

            let address = this.$root.user.addresses.find((address) => address.id == id)

            this.$root.checkout[type + '_address'] = Object.assign(
                {
                    customer_address_id: address.id,
                },
                address
            )
        },
    },

    created() {
        this.$root.$on('logout', this.onLogout)
    },

    asyncComputed: {
        user: function () {
            return this.getUser()
        },
    },
}
