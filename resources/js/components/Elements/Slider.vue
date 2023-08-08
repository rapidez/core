<script>
import { useElementHover, useIntervalFn, useEventListener, useThrottleFn } from '@vueuse/core'

export default {
    render() {
        return this.$scopedSlots.default({
            navigate: this.navigate,
            showLeft: this.showLeft,
            showRight: this.showRight,
            currentSlide: this.currentSlide,
            slidesVisible: this.slidesVisible,
            slidesTotal: this.slidesTotal,
        })
    },
    props: {
        reference: {
            type: String,
            default: 'slider'
        },
        vertical: {
            type: Boolean,
            default: false
        },
        interval: {
            type: Number,
            default: 5000
        },
        autoplay: {
            type: Boolean,
            default: false
        },
        bounce: {
            type: Boolean,
            default: false
        },
        stopOnHover: {
            type: Boolean,
            default: true
        },
        loop: {
            type: Boolean,
            default: false
        }
    },
    data: () => {
        return {
            slider: '',
            slidesTotal: 0,
            position: 0,
            showLeft: false,
            showRight: false,
            mounted: false,
            hover: false,
            direction: 1,
            chunk: '',
            pause: () => {},
            resume: () => {}
        }
    },
    mounted() {
        this.initSlider()
        useEventListener(this.slider, 'scroll', useThrottleFn(this.scroll, 150, true, true), { passive: true })
        this.$nextTick(() => {
            if (this.loop) {
                this.chunk = this.slider.cloneNode(true)
            }
            this.slider.dispatchEvent(new CustomEvent('scroll'))
            this.mounted = true

            this.initAutoPlay()
        })
    },
    methods: {
        initSlider() {
            this.slider = this.$scopedSlots.default()[0].context.$refs[this.reference]
            this.slidesTotal = (this.slider.children?.length ?? 1) - this.slidesVisible + 1
        },

        initAutoPlay() {
            if (!this.autoplay) {
                return
            }

            const { pause, resume } = useIntervalFn(this.autoScroll, this.interval)
            this.pause = pause
            this.resume = resume

            if (!this.stopOnHover) {
                return
            }
            this.hover = useElementHover(this.$el)
        },

        scroll() {
            this.position = this.vertical ? this.slider.scrollTop : this.slider.scrollLeft
            this.showLeft = this.loop || this.position
            this.showRight = this.loop || this.slider.offsetWidth + this.position < this.slider.scrollWidth - 1
        },

        autoScroll() {
            if (this.slidesTotal == 1) {
                return
            }
            let next = this.currentSlide + this.direction
            if (next >= this.slidesTotal || next < 0) {
                if (this.bounce) {
                    this.direction = -this.direction
                    next = this.currentSlide + this.direction
                } else {
                    next = 0
                }
            }
            this.navigate(next)
        },

        navigate(index) {
            this.vertical
                ? this.slider.scrollTo(0, this.slider.children[index]?.offsetTop)
                : this.slider.scrollTo(this.slider.children[0]?.offsetWidth * index, 0)
        },

        handleLoop() {
            if (this.currentSlide + 1 === this.slidesTotal - 1) {
                Array.from(this.chunk.children).forEach((child) => {
                    this.slider.appendChild(child.cloneNode(true))
                })
            }
            if (this.currentSlide < 1) {
                Array.from(this.chunk.children).forEach((child) => {
                    this.slider.insertBefore(child.cloneNode(true), this.slider.firstChild)
                })
            }
        }
    },
    watch: {
        hover(isHovering) {
            if (!isHovering || !this.stopOnHover) {
                this.resume()
            } else {
                this.pause()
            }
        },
        currentSlide() {
            this.initSlider()
            if (this.loop) {
                this.handleLoop()
            }
        }
    },
    computed: {
        currentSlide() {
            if (this.mounted) {
                return Math.round(this.position / this.childSpan)
            }
        },
        slidesVisible() {
            if (this.mounted) {
                return Math.round(this.sliderSpan / this.childSpan)
            }
        },
        childSpan() {
            if (this.mounted) {
                return this.vertical
                    ? this.slider.children[0]?.offsetHeight ?? this.slider.offsetHeight
                    : this.slider.children[0]?.offsetWidth ?? this.slider.offsetWidth
            }
        },
        sliderSpan() {
            if (this.mounted) {
                return this.vertical ? this.slider.offsetHeight : this.slider.offsetWidth
            }
        }
    }
}
</script>
