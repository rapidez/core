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
                Object.keys(children).forEach(key => {
                    Object.keys(children[key]).forEach(keyAttribute => {
                        if(keyAttribute === superAttributeCode && children[key][keyAttribute] == option) {
                            this.attributeOptions[keyAttribute] = option
                        }
                    })
                })

                let child;
                Object.keys(this.attributeOptions).forEach(key => {
                    let childArray = Object.values(children)
                    if(child === undefined) {
                        child = Object.values(children).filter(c => c.hasOwnProperty(key) && String(c[key]) === String(this.attributeOptions[key]));
                    } else {
                        child = child.filter(c => c.hasOwnProperty(key) && String(c[key]) === this.attributeOptions[key])
                    }
                })

                this.currentChild = child.shift();
                this.productImages = this.currentChild.images
            }
        }
    }
</script>
