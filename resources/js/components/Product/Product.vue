<script>
    export default {
        props: ['images'],
        render() {
            return this.$scopedSlots.default({
                changeAttribute: this.changeAttribute,
                productImages: this.productImages,
                attributeOptions: this.attributeOptions,
                getCurrentChild: this.getCurrentChild
            })
        },
        data() {
            return {
                productImages: null,
                attributeOptions: {},
                currentChild: null,
            }
        },

        methods: {
            changeAttribute(superAttributeCode, option) {
                let children = window.config.product.children
                this.attributeOptions[superAttributeCode] = Number(option)
                Object.keys(this.attributeOptions).forEach(key => {
                    children = Object.values(children).filter(c => c[key] === this.attributeOptions[key])
                })

                this.currentChild = this.getCurrentChild(children)
                this.productImages = this.currentChild.images
            },
            getCurrentChild(children) {
                let images = JSON.parse(this.images)
                let child
                if(children.length > 1) {
                    Object.keys(children).forEach(key => {
                        console.log(children[key].images.every((val, index) => val === images[index].value))
                        if(children[key].images.every((val, index) => val === images[index].value) === true) {
                            this.currentChild = children[key]
                            child = children[key]
                       }
                   })
                } else {
                    child = children.shift()
                }

                if(child === undefined) child = children.shift()

                return child
        }
    }
}
</script>
