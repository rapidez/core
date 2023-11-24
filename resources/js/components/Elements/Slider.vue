<script>
import { useElementHover, useIntervalFn, useEventListener, useThrottleFn } from '@vueuse/core'

export default {
    render() {
        return this.$scopedSlots.default(this)
    },
    props: {
        reference: {
            type: String,
            default: 'slider',
        },
        vertical: {
            type: Boolean,
            default: false,
        },
        interval: {
            type: Number,
            default: 5000,
        },
        autoplay: {
            type: Boolean,
            default: false,
        },
        bounce: {
            type: Boolean,
            default: false,
        },
        stopOnHover: {
            type: Boolean,
            default: true,
        },
        loop: {
            type: Boolean,
            default: false,
        },
    },
    data: () => {
        return {
            slider: '',
            position: 0,
            showLeft: false,
            showRight: false,
            mounted: false,
            hover: false,
            direction: 1,
            pause: () => {},
            resume: () => {},
        }
    },
    mounted() {
        this.initSlider()
        useEventListener(this.slider, 'scroll', useThrottleFn(this.scroll, 150, true, true), { passive: true })
        if (this.loop) {
            useEventListener(this.slider, 'scrollend', this.scrollend, { passive: true })
        }
        this.$nextTick(() => {
            if (this.loop) {
                this.initLoop()
            }
            this.slider.dispatchEvent(new CustomEvent('scroll'))
            this.mounted = true

            this.initAutoPlay()
        })
    },
    methods: {
        initSlider() {
            this.slider = this.$scopedSlots.default()[0].context.$refs[this.reference]
        },
        initLoop() {
            if (!this.loop) {
                return
            }

            const slides = Array.from(this.slides)
            if (!slides.length) {
                return
            }
            let firstChild = this.slider.firstChild

            for (let slide of slides) {
                let startClone = this.slider.insertBefore(slide.cloneNode(true), firstChild)
                startClone.dataset.clone = true
                startClone.dataset.position = 'start'

                let endClone = this.slider.appendChild(slide.cloneNode(true))
                endClone.dataset.clone = true
                endClone.dataset.position = 'end'
            }

            this.slider.dispatchEvent(new CustomEvent('scrollend'))
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
        scroll(event) {
            this.position = this.vertical ? event.target.scrollTop : event.target.scrollLeft
            this.showLeft = this.loop || this.position
            this.showRight = this.loop || this.slider.offsetWidth + this.position < this.slider.scrollWidth - 1
        },
        scrollend(event) {
            let scrollPosition = this.vertical ? event.target.scrollTop : event.target.scrollLeft
            if (scrollPosition < this.sliderStart) {
                this.slider.scrollTo({ [this.vertical ? 'top' : 'left']: scrollPosition + this.sliderStart, behavior: 'instant' })
            } else if (scrollPosition > this.sliderEnd) {
                this.slider.scrollTo({ [this.vertical ? 'top' : 'left']: scrollPosition - this.sliderStart, behavior: 'instant' })
            }
        },
        autoScroll() {
            if (this.slidesTotal == 1) {
                return
            }
            let next = this.currentSlide + this.direction
            if ((next >= this.slidesTotal && !this.loop) || next < 0) {
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
            index = this.loop ? index + this.slides.length : index

            this.vertical
                ? this.slider.scrollTo(0, this.slider.children[index]?.offsetTop)
                : this.slider.scrollTo(this.slider.children[index]?.offsetLeft, 0)
        },
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
        },
    },
    computed: {
        currentSlide() {
            if (!this.mounted) {
                return 0
            }
            return Math.round(this.position / this.childSpan) % this.slides.length
        },
        slidesVisible() {
            if (!this.mounted) {
                return 0
            }

            return Math.round(this.sliderSpan / this.childSpan)
        },
        childSpan() {
            if (!this.mounted) {
                return 0
            }

            return this.vertical
                ? this.slider.children[0]?.offsetHeight ?? this.slider.offsetHeight
                : this.slider.children[0]?.offsetWidth ?? this.slider.offsetWidth
        },
        sliderSpan() {
            if (!this.mounted) {
                return 0
            }

            return this.vertical ? this.slider.offsetHeight : this.slider.offsetWidth
        },
        slidesTotal() {
            if (!this.mounted) {
                return 0
            }

            return (this.slides?.length ?? 1) - (this.loop ? 0 : this.slidesVisible - 1)
        },
        slides() {
            return this.slider.querySelectorAll(':scope > :not([data-clone=true])')
        },
        sliderStart() {
            return this.vertical ? this.slides[0].offsetTop : this.slides[0].offsetLeft
        },
        sliderEnd() {
            let lastChild = this.slides[this.slides.length - 1]

            if (this.vertical) {
                return lastChild.offsetTop + lastChild.offsetHeight
            }

            return lastChild.offsetLeft + lastChild.offsetWidth
        },
    },
}
</script>
