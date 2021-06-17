<script>
    export default {
        data: () => ({
            images: false,
            opened: false,
        }),

        render() {
            return this.$scopedSlots.default({
                images: this.images,
                opened: this.opened,
                toggle: this.toggle,
            })
        },

        created() {
            let self = this
            this.$root.$on('productSuperAttributeChange', function (simpleProduct) {
                Object.values(window.config.product.children).forEach((child) => {
                    if (child === simpleProduct && Object.values(window.config.product.super_attributes).filter((attribute) => attribute.update_image).length) {
                        self.images = simpleProduct.images
                    }
                })
            })
        },

        methods: {
            toggle(index, event) {
                this.opened = this.opened === false ? index : false
                event.currentTarget.firstChild.src = event.currentTarget.href
            },
        }
    }
</script>
