<script>
    export default {
        render() {
            return this.$scopedSlots.default({
                changeAttribute: this.changeAttribute,
                productImages: this.productImages,
                attributeOptions: this.attributeOptions
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

                this.currentChild = children.shift()
                this.productImages = this.currentChild.images
            }
        }
    }
</script>
