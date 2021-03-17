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
            let self = this
            this.$root.$on('productSuperAttributeChange', function (simpleProduct) {
                if (Object.values(window.config.product.super_attributes).filter((attribute) => attribute.update_image).length) {
                    self.images = simpleProduct.images
                }
            })
        },
    }
</script>
