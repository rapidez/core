<script>
    import GetCart from './../Cart/mixins/GetCart'
    import Graphql from './../Graphql'

    export default {
        mixins: [GetCart, Graphql],
        created() {
            this.getMask()
            this.resolveData()
        },
        mounted() {
            this.$root.$on('refresh-cart', this.refreshCart)
        },
        methods: {
            async refreshCart()
            {
                await this.getMask()
                this.resolveData({'skip_cache': true})
            }
        }
    }
</script>
