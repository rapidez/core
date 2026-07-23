<script>
import { useElementHover, useIntervalFn, useEventListener, useThrottleFn, useResizeObserver } from '@vueuse/core'

export default {
    render() {
        return this.$slots.default(this)
    },
    props: {
        reference: {
            type: String,
            default: 'slider',
        },
        containerReference: {
            type: String,
            default: null,
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
            container: '',
            position: 0,
            showLeft: false,
            showRight: false,
            mounted: false,
            hover: false,
            direction: 1,
            pause: () => {},
            resume: () => {},

            childSpan: 0,
            sliderSpan: 0,
        }
    },
    mounted() {
        this.initSlider()
        useEventListener(this.slider, 'scroll', useThrottleFn(this.scroll, 150, true, true), { passive: true })
        if (this.loop) {
            useEventListener(this.slider, 'scrollend', this.scrollend, { passive: true })
        }
        this.$nextTick(() => {
            useResizeObserver(this.slider, useThrottleFn(this.updateSpan, 150, true, true))
            setTimeout(() => this.slider.dispatchEvent(new CustomEvent('scroll')))
            this.mounted = true
            if (this.loop) {
                this.initLoop()
            }

            this.initAutoPlay()
        })

        this.updateSpan()
    },
    methods: {
        initSlider() {
            this.slider = this.$slots.default(this)[0].ctx.refs[this.reference]
            if (Array.isArray(this.slider) && this.slider.length) {
                this.slider = this.slider[0]
            }
            this.container = this.containerReference ? this.$slots.default(this)[0].ctx.refs[this.containerReference] : this.slider
        },
        initLoop() {
            if (!this.loop) {
                return
            }

            const slides = Array.from(this.slides)
            if (!slides.length) {
                return
            }
            let firstChild = this.container.firstChild

            requestAnimationFrame(() => {
                for (let slide of slides) {
                    let startClone = this.container.insertBefore(slide.cloneNode(true), firstChild)
                    startClone.dataset.clone = true
                    startClone.dataset.position = 'start'

                    let endClone = this.container.appendChild(slide.cloneNode(true))
                    endClone.dataset.clone = true
                    endClone.dataset.position = 'end'
                }

                this.slider.dispatchEvent(new CustomEvent('scrollend'))
            })
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
            this.hover = useElementHover(this.$el.nextSibling)
        },
        scroll(event) {
            this.position = this.vertical ? event.target.scrollTop : event.target.scrollLeft
            this.showLeft = this.loop || this.position
            this.showRight = this.loop || this.container?.offsetWidth + this.position < this.container?.scrollWidth - 1
        },
        scrollend(event) {
            let scrollPosition = this.vertical ? event.target.scrollTop : event.target.scrollLeft
            if (scrollPosition < this.sliderStart) {
                this.slider.scrollTo({ [this.vertical ? 'top' : 'left']: scrollPosition + this.sliderStart, behavior: 'instant' })
            } else if (scrollPosition >= this.sliderEnd) {
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
        navigate(index, behavior = 'smooth') {
            index = Math.min(
                Math.max(index, 0),
                this.container?.children.length - 1
            )

            this.vertical
                ? this.slider.scrollTo({ top: this.container?.children[index]?.offsetTop, behavior: behavior })
                : this.slider.scrollTo({ left: this.container?.children[index]?.offsetLeft, behavior: behavior })
        },
        handleLoop() {
            requestAnimationFrame(() => {
                if (this.currentSlide + 1 === this.slidesTotal - 1) {
                    Array.from(this.chunk.children).forEach((child) => {
                        this.container.appendChild(child.cloneNode(true))
                    })
                }
                if (this.currentSlide < 1) {
                    Array.from(this.chunk.children).forEach((child) => {
                        this.container.insertBefore(child.cloneNode(true), this.container.firstChild)
                    })
                }
            })
        },
        updateSpan() {
            setTimeout(() => {
                let slide = this.childSpan == 0 ? 0 : this.currentSlide

                this.childSpan = this.vertical
                    ? (this.container?.children[0]?.offsetHeight ?? this.container?.offsetHeight)
                    : (this.container?.children[0]?.offsetWidth ?? this.container?.offsetWidth)

                this.navigate(slide, 'instant')

                this.sliderSpan = this.vertical ? this.container?.offsetHeight : this.container?.offsetWidth
            })
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
            // First make a calculated guess, improving performance by not having to loop through all slides
            const bestGuess = Math.round(this.position / this.childSpan) % this.slides.length
            const getSlideByGuess = (slide) => {
                const bestGuessChild = this.container.children[slide]
                if (!bestGuessChild) {
                    return slide;
                }
                const bestGuessSpan = this.vertical
                        ? bestGuessChild.offsetHeight
                        : bestGuessChild.offsetWidth
                const bestGuessOffset = this.vertical
                        ? bestGuessChild.offsetTop
                        : bestGuessChild.offsetLeft

                // Past halfway to the slide it will snap
                if (this.position + (bestGuessSpan / 2) < bestGuessOffset) {
                    return getSlideByGuess(slide - 1);
                }

                if (this.position >= bestGuessOffset + bestGuessSpan) {
                    return getSlideByGuess(slide + 1);
                }

                return slide;
            }

            return getSlideByGuess(bestGuess)
        },
        slidesVisible() {
            if (!this.mounted) {
                return 0
            }

            return Math.round(this.sliderSpan / (this.vertical ? this.container.scrollHeight : this.container.scrollWidth) * this.slides.length)
        },
        slidesTotal() {
            if (!this.mounted) {
                return 0
            }

            return (this.slides?.length ?? 1) - (this.loop ? 0 : this.slidesVisible - 1)
        },
        slides() {
            if (!this.mounted || !this.container) {
                return []
            }
            return this.container.querySelectorAll(':scope > :not([data-clone=true])')
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
