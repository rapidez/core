<script>
import { order, refresh as refreshOrder } from '../../stores/useOrder'
import { mask as useMask } from '../../stores/useMask'
import { token as useToken } from '../../stores/useUser'
import { clear as clearCart } from '../../stores/useCart'

export default {
    data() {
        return {
            order: order,
        }
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    created() {
        this.$root.$emit('checkout-success', this.order)
    },

    methods: {
        async refreshOrder() {
            await refreshOrder()
        },

        serialize(address) {
            return JSON.stringify({
                firstname: address.firstname ?? '',
                lastname: address.lastname ?? '',
                postcode: address.postcode ?? '',
                street: address.street ?? '',
                city: address.city ?? '',
                country_id: address.country_id ?? '',
                telephone: address.telephone ?? '',
            })
        },
        sameAddress(a1, a2) {
            return this.serialize(a1) == this.serialize(a2)
        },
    },

    computed: {
        hideBilling() {
            return this.order.shipping_address && this.order.billing_address && this.sameAddress(this.order.shipping_address, this.order.billing_address)
        }
    },
}
</script>
