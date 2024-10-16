<script>
import InteractWithUser from './../User/mixins/InteractWithUser'
import { useEventListener, useLocalStorage } from '@vueuse/core'
import useMask from '../../stores/useMask'
import { cart, hasOnlyVirtualItems, virtualItems, linkUserToCart } from '../../stores/useCart'
import { FetchError } from '../../fetch'

export default {
    mixins: [InteractWithUser],

    data() {
        return {
            backEvent: false,
            steps: [],
        }
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    created() {
        if (!cart.value?.total_quantity) {
            window.location.replace(window.url('/'))
            return
        }

        this.steps = this.config.checkout_steps[window.config.store_code] ?? this.config.checkout_steps['default']
        this.setupHistory()
        this.setupCheckout()
    },

    methods: {
        async setupCheckout() {
            await this.refreshUser(false)

            this.setCheckoutCredentialsFromDefaultUserAddresses()
            this.getShippingMethods()
            this.getTotalsInformation()

            this.$root.$emit('checkout-step', 1)
        },

        async getShippingMethods() {
            let responseData = await this.magentoCart('post', 'estimate-shipping-methods', { address: this.shippingAddress })
            this.checkout.shipping_methods = responseData

            if (responseData.length === 1) {
                this.checkout.shipping_method = responseData[0].carrier_code + '_' + responseData[0].method_code
            }
        },

        async getTotalsInformation() {
            let responseData = await this.magentoCart('post', 'totals-information', {
                addressInformation: {
                    address: {
                        countryId: this.shippingAddress.country_id,
                    },
                },
            })

            this.checkout.totals = responseData
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

                if (!hasOnlyVirtualItems.value) {
                    addressInformation.shipping_carrier_code = this.currentShippingMethod?.carrier_code
                    addressInformation.shipping_method_code = this.currentShippingMethod?.method_code
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
                        if (self.$root.cart?.entity_id) {
                            await linkUserToCart()
                            let mask = useMask('mask')
                            mask.value = self.$root.cart.entity_id
                        }
                    })
                }

                let responseData = await this.magentoCart('post', 'shipping-information', {
                    addressInformation: addressInformation,
                })

                this.checkout.payment_methods = responseData.payment_methods
                this.checkout.totals = responseData.totals
                this.$root.$emit('checkout-credentials-saved')
                return true
            } catch (error) {
                console.error(error)
                return false
            }
        },

        validateCredentials() {
            let validated = true
            if (!this.checkout.shipping_method && !hasOnlyVirtualItems.value) {
                Notify(window.config.translations.checkout.no_shipping_method, 'warning')
                validated = false
            }

            if (validated && this.checkout.create_account && this.checkout.password != this.checkout.password_repeat) {
                Notify(window.config.translations.account.password_mismatch, 'warning')
                validated = false
            }

            if (!this.checkout.shipping_method && hasOnlyVirtualItems.value && validated) {
                return true
            }

            const optionalFields = Object.keys(
                Object.fromEntries(
                    Object.entries(window.config.show_customer_address_fields).filter(([key, value]) => !value || value === 'opt'),
                ),
            )
            Object.entries(this.checkout.shipping_address).forEach(([key, val]) => {
                if (!val && !['region_id', 'customer_address_id', 'same_as_billing'].concat(optionalFields).includes(key)) {
                    Notify(key + ' cannot be empty', 'warning')
                    validated = false
                }
            })

            return validated
        },

        async selectShippingMethod() {
            let responseData = await this.magentoCart('post', 'shipping-information', {
                addressInformation: {
                    shipping_address: this.shippingAddress,
                    billing_address: this.billingAddress,
                    shipping_carrier_code: this.currentShippingMethod?.carrier_code,
                    shipping_method_code: this.currentShippingMethod?.method_code,
                },
            })
            this.checkout.totals = responseData.totals
        },

        async selectPaymentMethod() {
            let response = await this.magentoCart('post', 'set-payment-information', {
                email: this.user?.email ? this.user.email : this.$root.guestEmail,
                paymentMethod: {
                    method: this.checkout.payment_method,
                },
            })

            this.$root.$emit('checkout-payment-selected', {
                method: this.checkout.payment_method,
            })

            this.getTotalsInformation()
        },

        async savePaymentMethod() {
            if (!this.checkout.payment_method) {
                Notify(window.config.translations.checkout.no_payment_method, 'error')
                return false
            }

            try {
                this.$root.$emit('before-checkout-payment-saved', {
                    order: {
                        payment_method_code: this.checkout.payment_method,
                    },
                })

                let response = {}
                if (!window.app.checkout?.preventOrder) {
                    response = await this.magentoCart('post', 'payment-information', {
                        billingAddress: this.billingAddress,
                        shippingAddress: this.shippingAddress,
                        email: this.user?.email ? this.user.email : this.$root.guestEmail,
                        paymentMethod: {
                            method: this.checkout.payment_method,
                            extension_attributes: {
                                agreement_ids: this.checkout.agreement_ids,
                            },
                        },
                    })
                }
                // response = orderId

                this.$root.$emit('checkout-payment-saved', {
                    order: {
                        id: response,
                        payment_method_code: this.checkout.payment_method,
                    },
                })

                return true
            } catch (error) {
                if (FetchError.prototype.isPrototypeOf(error)) {
                    const response = await error.response.json()
                    Notify(response?.message, 'error', response?.parameters)
                }
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

        setupHistory() {
            useEventListener('hashchange', () => {
                this.backEvent = true
                this.checkout.step = this.steps.indexOf(window.location.hash.substring(1))
            })

            history.replaceState(null, null, '#' + this.steps[this.checkout.step])
        },
    },

    computed: {
        checkout: function () {
            return this.$root.checkout
        },
        forceAccount: function () {
            return !virtualItems.value
        },
        shippingAddress: function () {
            let address = this.removeUnusedAddressInfo(this.$root.checkout.shipping_address)

            address.same_as_billing = Number(this.checkout.hide_billing)

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

        'checkout.billing_address.customer_address_id': function (customerAddressId) {
            this.setCustomerAddressByAddressId('billing', customerAddressId)
        },
        currentShippingMethod: function () {
            this.selectShippingMethod()
        },
        'checkout.payment_method': function () {
            this.selectPaymentMethod()
        },
        'checkout.shipping_address.country_id': function () {
            if (!cart.value?.id) {
                return
            }

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
