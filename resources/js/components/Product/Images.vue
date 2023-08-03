<script>
export default {
    data: () => ({
        images: config.product.images,
        active: 0,
        zoomed: false,
    }),

    render() {
        return this.$scopedSlots.default({
            images: this.images,
            active: this.active,
            zoomed: this.zoomed,
            toggleZoom: this.toggleZoom,
            change: this.change,
        })
    },

    created() {
        let self = this
        this.$root.$on('product-super-attribute-change', function (simpleProduct) {
            Object.values(window.config.product.children).forEach((child) => {
                if (
                    child === simpleProduct &&
                    Object.values(window.config.product.super_attributes).filter((attribute) => attribute.update_image).length
                ) {
                    self.images = simpleProduct.images
                }
            })
        })
    },

    methods: {
        toggleZoom() {
            this.zoomed = !this.zoomed

            if (this.zoomed) {
                document.addEventListener('keyup', this.keyPressed)
            } else {
                document.removeEventListener('keyup', this.keyPressed)
            }
        },

        keyPressed() {
            if (event.keyCode == 37 && this.active) {
                // left
                this.active--
            } else if (event.keyCode == 39 && this.active != this.images.length - 1) {
                // right
                this.active++
            } else if (event.keyCode == 27) {
                // esc
                this.toggleZoom()
            }
        },

        change(index) {
            this.active = index
        },
    },
}
</script>
