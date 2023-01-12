import { useLocalStorage, StorageSerializers, set } from "@vueuse/core"

let token = useLocalStorage('token', '', {serializer: StorageSerializers.string});

export default {
    methods: {
        async getUser() {
            if (token.value && !this.$root.user?.id) {
                await this.refreshUser()
            }
            return this.$root.user
        },

        async refreshUser(redirect = true) {
            try {
                let response = await magentoUser.get('customers/me')
                this.$root.user = response.data
            } catch (error) {
                if (error.response.status == 401) {
                    token.value = null;
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
                token.value = response.data

                window.magentoUser.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;

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

        logout(redirect = '/') {
            this.$root.$emit('logout', {'redirect': redirect})
        },

        onLogout(data = {}) {
            this.$root.user = null
            this.$root.guestEmail = null
            token.value = null
            Turbolinks.clearCache()
            window.location.href = data?.redirect ?? '/'
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
                    password: customer.password
                })
                return response.data
            } catch (error) {
                Notify(error.response.data.message, 'error', error.response.data?.parameters)
                return false
            }
        },

        setCheckoutCredentialsFromDefaultUserAddresses() {
            if (this.$root && this.$root.user?.id) {
                this.setCustomerAddressByAddressId('shipping', this.$root.user.default_shipping)
                this.setCustomerAddressByAddressId('billing', this.$root.user.default_billing)
            }
        },

        setCustomerAddressByAddressId(type, id) {
            if (!id) {
                this.$root.checkout[type + '_address'].customer_address_id = null;
                return
            }

            let address = this.$root.user.addresses.find((address) => address.id == id)

            this.$root.checkout[type + '_address'] = Object.assign(
                this.$root.checkout[type + '_address'],
                Object.assign({
                    customer_address_id: address.id
                }, address))
        },
    },

    created() {
        this.$root.$on('logout', this.onLogout);
    },

    asyncComputed: {
        user: function () {
            return this.getUser()
        }
    }
}
