<script>
import { mask as useMask } from '../../stores/useMask'
import { token as useToken } from '../../stores/useUser'
import { clear as clearCart } from '../../stores/useCart'

export default {
    props: {
        token: {
            type: String,
            default: useToken.value,
        },
        mask: {
            type: String,
            default: useMask.value,
        },
    },

    data() {
        return {
            order: {},
        }
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    created() {
        let successStep = this.$root.getCheckoutStep('success')
        if (successStep > 0) {
            this.$root.checkout.step = successStep
            this.$root.$emit('checkout-step', successStep)
        }

        this.refreshOrder().then(() => {
            this.$root.$emit('checkout-success', this.order)
            clearCart()
        })
    },

    methods: {
        async refreshOrder() {
            this.order = await window
                .rapidezAPI(
                    'get',
                    'order',
                    {},
                    {
                        headers: { Authorization: 'Bearer ' + (this.token || this.mask) },
                    },
                )
                .catch((error) => Turbo.visit(window.url('/')))
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
            return this.shipping && this.billing && this.sameAddress(this.shipping, this.billing)
        },
        shipping() {
            if (!this.order?.sales_order_addresses) {
                return null
            }
            let shipping = this.order.sales_order_addresses.filter((e) => e.address_type == 'shipping')
            return shipping.length > 1 ? null : shipping.at(-1)
        },
        billing() {
            if (!this.order?.sales_order_addresses) {
                return null
            }
            let billing = this.order.sales_order_addresses.filter((e) => e.address_type == 'billing')
            return billing.at(-1)
        },
        items() {
            if (!this.order?.sales_order_addresses) {
                return []
            }
            return this.order.sales_order_items.filter((item) => !item.parent_item_id)
        },
    },
}
</script>
