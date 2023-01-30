import { useLocalStorage } from "@vueuse/core"
import { user, token, refresh as refreshUser, clear as clearUser } from "../../../stores/useUser"

export default {
    methods: {
        async getUser() {
            return user
        },

        async refreshUser(redirect = true) {
            const success = await refreshUser()

            if (!success && redirect) {
                Turbo.visit('/login')
            }
        },

        async login(username, password, loginCallback = false) {
            await magento.post('integration/customer/token', {
                username: username,
                password: password
            })
            .then(async(response) => {
                token.value = response.data

                await this.refreshUser(false)

                this.setCheckoutCredentialsFromDefaultUserAddresses()
                await window.app.$emit('logged-in')
                if (loginCallback) {
                    await loginCallback()
                }
            })
            .catch((error) => {
                Notify(error.response.data.message, 'error', error.response.data?.parameters)
                console.error(error)

                return false
            })
        },

        logout(redirect = '/') {
            this.$root.$emit('logout', {'redirect': redirect})
        },

        onLogout(data = {}) {
            clearUser()
            useLocalStorage('email', '').value = ''
            Turbo.cache.clear()
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
                console.error(error)

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
