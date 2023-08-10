<script>
import GetCart from './../Cart/mixins/GetCart'
import InteractWithUser from './../User/mixins/InteractWithUser'

export default {
    mixins: [GetCart, InteractWithUser],

    data() {
        return {
            backEvent: false,
            steps: [],
        }
    },

    render() {
        return this.$scopedSlots.default({
            hasItems: this.hasItems,
            cart: this.cart,
            checkout: this.checkout,
            save: this.save,
            goToStep: this.goToStep,
            getTotalsInformation: this.getTotalsInformation,
        })
    },

    created() {
        if (!this.hasItems) {
            window.location.replace(window.url('/'))
            return
        }

        this.checkout.hasVirtualItems = this.hasVirtualItems
        this.steps = this.config.checkout_steps[window.config.store_code] ?? this.config.checkout_steps['default']
        this.setupHistory()
        this.setCheckoutCredentialsFromDefaultUserAddresses()
        this.getShippingMethods()
        this.getTotalsInformation()
        this.$root.$emit('checkout-step', 1)
    },

    methods: {
        async getShippingMethods() {
            try {
                let response = await this.magentoCart('post', 'estimate-shipping-methods', {
                    address: {
                        country_id: this.checkout.shipping_address.country_id,
                    },
                })
                this.checkout.shipping_methods = response.data

                if (response.data.length === 1) {
                    this.checkout.shipping_method = response.data[0].carrier_code + '_' + response.data[0].method_code
                }

                return true
            } catch (error) {
                if ([401, 404].includes(error.response.status)) {
                    this.logout('/login')
                } else {
                    Notify(error.response.data.message, 'error', error.response.data?.parameters)
                }
                return false
            }
        },

        async getTotalsInformation() {
            try {
                let response = await this.magentoCart('post', 'totals-information', {
                    addressInformation: {
                        address: {
                            countryId: this.checkout.shipping_address.country_id,
                        },
                    },
                })

                this.checkout.totals = response.data

                return true
            } catch (error) {
                if ([401, 404].includes(error.response.status)) {
                    this.logout('/login')
                } else {
                    Notify(error.response.data.message, 'error', error.response.data?.parameters)
                }
                return false
            }
        },

        async save(savedItems, targetStep) {
            let validated = true
            await this.asyncForEach(savedItems, async (item) => {
                switch (item) {
                    case 'credentials':
                        if (!(await this.saveCredentials())) {
                            validated = false
                        }
                        break
                    case 'payment_method':
                        if (!(await this.savePaymentMethod())) {
                            validated = false
                        }
                        break
                    default:
                        Notify('Unknown item to save', 'error')
                }
            })

            if (validated && !this.$root.checkout.doNotGoToTheNextStep) {
                this.goToStep(targetStep)
            }
        },

        goToStep(step) {
            if (step === 0) {
                Turbo.visit(window.url('/cart'))
                return
            }

            this.$root.$emit('checkout-step', step)

            this.checkout.step = step
        },

        async saveCredentials() {
            if (!this.validateCredentials()) {
                return false
            }

            try {
                let addressInformation = {
                    shipping_address: this.shippingAddress,
                    billing_address: this.billingAddress,
                }

                if (!this.hasOnlyVirtualItems) {
                    addressInformation.shipping_carrier_code = this.currentShippingMethod.carrier_code
                    addressInformation.shipping_method_code = this.currentShippingMethod.method_code
                }

                if (this.checkout.create_account && this.checkout.password) {
                    let customer = await this.createCustomer({
                        email: this.$root.guestEmail,
                        password: this.checkout.password,
                        firstname: this.shippingAddress.firstname,
                        lastname: this.shippingAddress.lastname,
                    })
                    if (!customer) {
                        return false
                    }
                    let self = this
                    await this.login(customer.email, this.checkout.password, async () => {
                        if (self.$root.cart) {
                            await self.linkUserToCart()
                            localStorage.mask = self.$root.cart.entity_id
                        }
                    })
                }

                let response = await this.magentoCart('post', 'shipping-information', {
                    addressInformation: addressInformation,
                })

                this.checkout.payment_methods = response.data.payment_methods
                this.checkout.totals = response.data.totals
                this.$root.$emit('checkout-credentials-saved')
                return true
            } catch (error) {
                Notify(error.response.data.message, 'error', error.response.data?.parameters)
                return false
            }
        },

        validateCredentials() {
            let validated = true
            if (!this.checkout.shipping_method && !this.hasOnlyVirtualItems) {
                Notify(window.config.translations.checkout.no_shipping_method, 'warning')
                validated = false
            }

            if (validated && this.checkout.create_account && this.checkout.password != this.checkout.password_repeat) {
                Notify(window.config.translations.account.password_mismatch, 'warning')
                validated = false
            }

            if (!this.checkout.shipping_method && this.hasOnlyVirtualItems && validated) {
                return true
            }

            const optionalFields = Object.keys(
                Object.fromEntries(Object.entries(window.config.customer_fields_show).filter(([key, value]) => !value || value === 'opt')),
            )
            Object.entries(this.checkout.shipping_address).forEach(([key, val]) => {
                if ((val == undefined || val == null) && !['region_id', 'customer_address_id'].concat(optionalFields).includes(key)) {
                    Notify(key + ' cannot be empty', 'warning')
                    validated = false
                }
            })

            return validated
        },

        async selectShippingMethod() {
            let response = await this.magentoCart('post', 'shipping-information', {
                addressInformation: {
                    shipping_address: this.shippingAddress,
                    billing_address: this.billingAddress,
                    shipping_carrier_code: this.currentShippingMethod.carrier_code,
                    shipping_method_code: this.currentShippingMethod.method_code,
                },
            })
            this.checkout.totals = response.data.totals
        },

        async selectPaymentMethod() {
            let response = await this.magentoCart('post', 'set-payment-information', {
                email: this.user == null ? this.$root.guestEmail : this.user.email,
                paymentMethod: {
                    method: this.checkout.payment_method,
                },
            })
            this.getTotalsInformation()
        },

        async savePaymentMethod() {
            if (!this.checkout.payment_method) {
                Notify(window.config.translations.checkout.no_payment_method, 'error')
                return false
            }

            try {
                let response = await this.magentoCart('post', 'payment-information', {
                    billingAddress: this.billingAddress,
                    shippingAddress: this.shippingAddress,
                    email: this.user ? this.user.email : this.$root.guestEmail,
                    paymentMethod: {
                        method: this.checkout.payment_method,
                        extension_attributes: {
                            agreement_ids: this.checkout.agreement_ids,
                        },
                    },
                })
                // response.data = orderId

                this.$root.$emit('checkout-payment-saved', {
                    order: {
                        id: response.data,
                        payment_method_code: this.checkout.payment_method,
                    },
                })

                return true
            } catch (error) {
                Notify(error.response.data.message, 'error', error.response.data?.parameters)
                return false
            }
        },

        removeUnusedAddressInfo(address) {
            ;['id', 'region', 'region_id', 'customer_id', 'default_shipping', 'default_billing'].forEach((key) => delete address[key])

            if (!address.customer_address_id) {
                address.save_in_address_book = 1
            }

            return address
        },

        storeCredentials(type) {
            Object.keys(this.checkout[type + '_address']).forEach((key) => {
                let value = this.checkout[type + '_address'][key]
                let storageKey = type + '_' + key
                if (value !== '') {
                    localStorage[storageKey] = value
                } else {
                    localStorage.removeItem(storageKey)
                }
            })
        },

        setupHistory() {
            window.addEventListener(
                'hashchange',
                () => {
                    this.backEvent = true
                    this.checkout.step = this.steps.indexOf(window.location.hash.substring(1))
                },
                false,
            )

            history.replaceState(null, null, '#' + this.steps[this.checkout.step])
        },
    },

    computed: {
        checkout: function () {
            return this.$root.checkout
        },
        shippingAddress: function () {
            let address = this.removeUnusedAddressInfo(this.$root.checkout.shipping_address)

            if (this.checkout.hide_billing !== undefined && this.checkout.hide_billing !== null) {
                address.same_as_billing = Number(this.checkout.hide_billing)
            }

            return address
        },
        billingAddress: function () {
            if (this.checkout.hide_billing) {
                return this.shippingAddress
            }

            return this.removeUnusedAddressInfo(this.$root.checkout.billing_address)
        },
        currentShippingMethod: function () {
            return this.checkout.shipping_methods.find((method) => {
                return method.carrier_code + '_' + method.method_code === this.checkout.shipping_method
            })
        },
    },
    watch: {
        'checkout.shipping_address.customer_address_id': function (customerAddressId) {
            this.setCustomerAddressByAddressId('shipping', customerAddressId)
        },
        'checkout.shipping_address': {
            deep: true,
            handler: function () {
                this.storeCredentials('shipping')
            },
        },
        'checkout.billing_address.customer_address_id': function (customerAddressId) {
            this.setCustomerAddressByAddressId('billing', customerAddressId)
        },
        'checkout.billing_address': {
            deep: true,
            handler: function () {
                this.storeCredentials('billing')
            },
        },
        'checkout.hide_billing': function (value) {
            localStorage.hide_billing = value
        },
        'checkout.shipping_method': function () {
            this.selectShippingMethod()
        },
        'checkout.payment_method': function () {
            this.selectPaymentMethod()
        },
        'checkout.shipping_address.country_id': function () {
            this.getShippingMethods()
        },
        'checkout.step': function () {
            if (this.backEvent) {
                this.backEvent = false
                history.replaceState(null, null, '#' + this.steps[this.checkout.step])
                return
            }

            history.pushState(null, null, '#' + this.steps[this.checkout.step])
        },
    },
}
</script>
