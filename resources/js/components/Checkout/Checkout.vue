<script>
    import GetCart from './../Cart/mixins/GetCart'

    export default {
        mixins: [GetCart],

        render() {
            return this.$scopedSlots.default({
                hasItems: this.hasItems,
                cart: this.cart,
                checkout: this.checkout,
                inputChange: this.inputChange,
                save: this.save,
            })
        },

        created() {
            if (!this.hasItems) {
                window.location.replace('/')
                return
            }

            this.getShippingMethods()
        },

        methods: {
            inputChange(type, e) {
                this.checkout[type][e.target.id] = e.target.value
            },

            hasOnlyVirtualItems() {
                return Object.values(this.cart.items).filter((item) => item.type == 'downloadable').length === Object.values(this.cart.items).length
            },

            async getShippingMethods() {
                try {
                    let response = await this.magentoCart('post', 'estimate-shipping-methods', {
                        address: {
                            country_id: 'NL',
                        }
                    })
                    this.checkout.shipping_methods = response.data

                    if (response.data.length === 1) {
                        this.checkout.shipping_method = response.data[0].carrier_code+'_'+response.data[0].method_code
                    }

                    return true
                } catch (error) {
                    alert(error.response.data.message)
                    return false
                }
            },

            async save(savedItems, goToStep) {
                let validated = true
                await this.asyncForEach(savedItems, async item => {
                    switch(item) {
                        case 'credentials':
                            if (!await this.saveCredentials()) {
                                validated = false
                            }
                            break
                        case 'payment_method':
                            if (!await this.savePaymentMethod()) {
                                validated = false
                            }
                            break
                        default:
                            alert('Unknown item to save')
                    }
                })

                if (validated && !this.$root.checkout.doNotGoToTheNextStep) {
                    this.checkout.step = goToStep
                }
            },

            async saveCredentials() {
                if (!this.validateCredentials()) {
                    return false
                }

                try {
                    let addressInformation = {
                        shipping_address: this.shippingAddress
                    }

                    if (!this.hasOnlyVirtualItems()) {
                        addressInformation.shipping_carrier_code = this.checkout.shipping_method.split('_')[0]
                        addressInformation.shipping_method_code = this.checkout.shipping_method.split('_')[1]
                    }

                    let response = await this.magentoCart('post', 'shipping-information', {
                        addressInformation: addressInformation
                    })

                    this.checkout.payment_methods = response.data.payment_methods
                    this.$root.$emit('CheckoutCredentialsSaved')
                    return true
                } catch (error) {
                    alert(error.response.data.message)
                    return false
                }
            },

            validateCredentials() {
                let validated = true
                if (!this.checkout.shipping_method && !this.hasOnlyVirtualItems()) {
                    alert('No shipping method selected')
                    validated = false
                }

                if (!this.checkout.shipping_method && this.hasOnlyVirtualItems()) {
                    return true
                }

                Object.entries(this.checkout.shipping_address).forEach(([key, val]) => {
                    if (!val) {
                        alert(key + ' cannot be empty')
                        validated = false
                    }
                });

                return validated
            },

            async savePaymentMethod() {
                if (!this.checkout.payment_method) {
                    alert('No payment method selected')
                    return false
                }

                try {
                    let response = await this.magentoCart('post', 'payment-information', {
                        billingAddress: this.shippingAddress,
                        email: this.$root.guestEmail,
                        paymentMethod: {
                            method: this.checkout.payment_method
                        }
                    })
                    // response.data = orderId
                    localStorage.removeItem('mask')
                    localStorage.removeItem('cart')
                    this.$root.cart = null
                    this.$root.$emit('CheckoutPaymentSaved')
                    return true
                } catch (error) {
                    alert(error.response.data.message)
                    return false
                }
            }
        },

        computed: {
            checkout: function () {
                return this.$root.checkout
            },

            shippingAddress: function () {
                return {
                    firstname: this.checkout.shipping_address.firstname,
                    lastname: this.checkout.shipping_address.lastname,
                    postcode: this.checkout.shipping_address.zipcode,
                    street: [
                        this.checkout.shipping_address.street,
                        this.checkout.shipping_address.housenumber
                    ],
                    city: this.checkout.shipping_address.city,
                    country_id: 'NL',
                    telephone: this.checkout.shipping_address.telephone,
                }
            }
        }
    }
</script>
