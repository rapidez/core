<script>
    export default {
        data: () => ({
            images: false,
        }),

        render() {
            return this.$scopedSlots.default({
                images: this.images,
            })
        },

        created() {
            this.$root.$on('productSuperAttributeChange', (simpleProduct) => this.images = simpleProduct.images)
        },
    }
</script>
