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
                default: null
            }
        },

        data() {
            return {
                order: {}
            }
        },

        render() {
            return this.$scopedSlots.default({
                order: this.order,
                refreshOrder: this.refreshOrder,
            })
        },

        created() {
            this.token ??= localStorage.token;
            this.mask ??= localStorage.mask;

            this.refreshOrder();
            this.clearCart()
        },

        methods: {
            refreshOrder()
            {
                axios.get('/api/order/' + (this.token || this.mask))
                    .then((response) => this.order = response.data);
            }
        }
    }
</script>
