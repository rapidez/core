<script>
import { useEventListener } from '@vueuse/core'

export default {
    data: () => ({
        images: config.product.images,
        active: 0,
        zoomed: false,
        touchStartX: 0,
        stopKeyUpListener: () => {},
    }),

    mounted() {
        if (this.isTouchDevice()) {
            useEventListener(this.$el, 'touchstart', this.touchStart)
            useEventListener(this.$el, 'touchend', this.touchEnd)
        }
    },

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
                    self.active = Math.min(self.active, self.images.length - 1)
                }
            })
        })
    },

    methods: {
        toggleZoom() {
            this.zoomed = !this.zoomed

            if (this.zoomed) {
                this.stopKeyUpListener = useEventListener('keyup', this.keyPressed, { passive: true })
            } else {
                this.stopKeyUpListener()
                this.stopKeyUpListener = () => {}
            }
        },

        keyPressed(e) {
            if (e.key == 'ArrowLeft' && this.active > 0) {
                // left
                this.active--
            } else if (e.key == 'ArrowRight' && this.active < this.images.length - 1) {
                // right
                this.active++
            } else if (e.key == 'Escape') {
                // esc
                this.toggleZoom()
            }
        },

        isTouchDevice() {
            return 'ontouchstart' in window
        },

        touchStart(event) {
            this.touchStartX = event.touches[0].clientX
        },

        touchEnd(event) {
            const touchEndX = event.changedTouches[0].clientX
            const distance = touchEndX - this.touchStartX

            if (Math.abs(distance) < 20) {
                return
            }

            if (distance < 0 && this.active < this.images.length - 1) {
                this.change(this.active + 1)
            } else if (distance > 0 && this.active > 0) {
                this.change(this.active - 1)
            }
        },

        change(index) {
            this.active = index
        },
    },
}
</script>
