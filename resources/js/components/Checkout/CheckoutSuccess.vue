<script>
    import GetCart from './../Cart/mixins/GetCart'

    export default {
        mixins: [GetCart],

        render() {
            return this.$scopedSlots.default({
                order: this.order
            })
        },

        data: () => ({
            order: {
                email: '',
                shippingAddress: {},
                billingAddress: {},
                items: {},
                shippingMethod: '',
                paymentMethod: ''
            }
        }),

        created() {
            this.order.email = localStorage.email
            this.order.shippingAddress = {
                street: localStorage.shipping_street.split(','),
                postcode: localStorage.shipping_postcode,
                company: localStorage.shipping_company,
                country_id: localStorage.shipping_country_id,
                city: localStorage.shipping_city,
                firstname: localStorage.shipping_firstname,
                lastname: localStorage.shipping_lastname,
                telephone: localStorage.shipping_telephone
            }

            if(localStorage.shipping_same_as_billing) {
                this.order.billingAddress = this.order.shippingAddress
            } else {
                this.order.billingAddress = {
                    street: localStorage.billing_street.split(','),
                    postcode: localStorage.billing_postcode,
                    company: localStorage.billing_company,
                    country_id: localStorage.billing_country_id,
                    city: localStorage.billing_city,
                    firstname: localStorage.billing_firstname,
                    lastname: localStorage.billing_lastname,
                    telephone: localStorage.billing_telephone
                }
            }
            this.order.items = this.$root.cart.items
            this.order.shippingMethod = this.$root.checkout.shipping_methods.find(method => method.carrier_code === this.$root.checkout.shipping_method.split('_')[0]).method_title
            this.order.paymentMethod = this.$root.checkout.payment_methods.find(method => method.code === this.$root.checkout.payment_method).title
            this.clearCart()
        }
    }
</script>
