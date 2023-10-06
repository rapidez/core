<script>
import GetCart from './../Cart/mixins/GetCart'

export default {
    mixins: [GetCart],

    props: {
        token: {
            type: String,
            default: null,
        },
        mask: {
            type: String,
            default: null,
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
        this.token ??= localStorage.token
        this.mask ??= localStorage.mask

        this.refreshOrder()
        this.clearCart()
    },

    methods: {
        refreshOrder() {
            axios
                .get(window.url('/api/order'), { headers: { Authorization: 'Bearer ' + (this.token || this.mask) } })
                .then((response) => (this.order = response.data))
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
            return this.order.sales_order_items.filter((item) => !item.parent_item_id)
        },
    },
}
</script>
