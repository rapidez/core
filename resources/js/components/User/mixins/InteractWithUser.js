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
            try {
                let response = await magentoUser.get('customers/me')
                localStorage.user = JSON.stringify(response.data)
                window.app.user = response.data
            } catch (error) {
                if (error.response.status == 401) {
                    localStorage.removeItem('token')
                }
                if (redirect) {
                    Turbolinks.visit('/login')
                }
            }
        },

        async login(username, password, loginCallback = false) {
            await magento.post('integration/customer/token', {
                username: username,
                password: password
            })
            .then(async(response) => {
                localStorage.token = response.data
                window.magentoUser.defaults.headers.common['Authorization'] = `Bearer ${localStorage.token}`;
                await this.refreshUser(false)
                this.setCheckoutCredentialsFromDefaultUserAddresses()
                if (loginCallback) {
                    await loginCallback()
                }
            })
            .catch((error) => {
                alert(error.response.data.message)
                return false
            })
        },

        logout(redirect = '/') {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            localStorage.removeItem('mask')
            localStorage.removeItem('cart')
            this.$root.user = null
            Turbolinks.clearCache()
            window.location.href = redirect
        },

        async createCustomer(shippingAddress, billingAddress, password) {
            try {
                let response = await magentoUser.post('customers', {
                    customer: {
                        email: this.$root.guestEmail,
                        firstname: shippingAddress.firstname,
                        lastname: shippingAddress.lastname,
                        addresses: [
                            {
                                defaultShipping: true,
                                firstname: shippingAddress.firstname,
                                lastname: shippingAddress.lastname,
                                postcode: shippingAddress.postcode,
                                street: shippingAddress.street,
                                city: shippingAddress.city,
                                countryId: shippingAddress.country_id,
                                telephone:  shippingAddress.telephone
                            },
                            {
                                defaultBilling: true,
                                firstname: billingAddress.firstname,
                                lastname: billingAddress.lastname,
                                postcode: billingAddress.postcode,
                                street: billingAddress.street,
                                city: billingAddress.city,
                                countryId: billingAddress.country_id,
                                telephone:  billingAddress.telephone
                            }
                        ]
                    },
                    password: password
                })
                return response.data
            } catch (error) {
                alert(error.response.data.message)
                return false
            }
        },

        setCheckoutCredentialsFromDefaultUserAddresses() {
            if (this.$root && this.$root.user) {
                if (this.$root.user.default_shipping) {
                    let address = this.$root.user.addresses.find((address) => address.id == this.$root.user.default_shipping)
                    this.$root.checkout.shipping_address = address
                }

                if (this.$root.user.default_billing) {
                    let address = this.$root.user.addresses.find((address) => address.id == this.$root.user.default_billing)
                    this.$root.checkout.billing_address = address
                }
            }
        },
    },

    asyncComputed: {
        user: function () {
            return this.getUser()
        }
    }
}
