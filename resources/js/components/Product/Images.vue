<script>
import { useEventListener } from '@vueuse/core'

export default {
    data: () => ({
        images: config.product.images,
        active: 0,
        zoomed: false,
        stopKeyUpListener: () => {},
    }),

    render() {
        return this.$scopedSlots.default(this)
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
                this.stopKeyUpListener = useEventListener('keyup', this.keyPressed, {passive: true})
            } else {
                this.stopKeyUpListener();
                this.stopKeyUpListener = () => {};
            }
        },

        keyPressed(e) {
            if (e.key == "ArrowLeft" && this.active) {
                // left
                this.active--
            } else if (e.key == "ArrowRight" && this.active != this.images.length - 1) {
                // right
                this.active++
            } else if (e.key == "Escape") {
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
