<script>
import GetCart from './../Cart/mixins/GetCart'

export default {
    mixins: [GetCart],

    data: () => {
        return {
            couponCode: '',
        }
    },

    render() {
        return this.$scopedSlots.default({
            cart: this.$root.cart,
            removeCoupon: this.removeCoupon,
            applyCoupon: this.applyCoupon,
            couponCode: this.couponCode,
            inputEvents: {
                input: (e) => {
                    this.couponCode = e.target.value
                },
            },
        })
    },

    methods: {
        applyCoupon() {
            self = this
            if (this.couponCode) {
                this.magentoCart('put', 'coupons/' + this.couponCode)
                    .then(function () {
                        self.refreshCart()
                        self.couponCode = ''
                        Notify(window.config.translations.cart.coupon.applied, 'success')
                    })
                    .catch((error) => Notify(error.response.data.message, 'error', error.response.data?.parameters))
            }
        },

        removeCoupon() {
            self = this
            this.magentoCart('delete', 'coupons')
                .then(function () {
                    self.refreshCart()
                })
                .catch((error) => Notify(error.response.data.message, 'error', error.response.data?.parameters))
        },
    },
}
</script>
