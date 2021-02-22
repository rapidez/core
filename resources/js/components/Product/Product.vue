<script>
    export default {
        props: ['product'],
        render() {
            return this.$scopedSlots.default({
                changeAttribute: this.changeAttribute,
                productImages: this.productImages
            })
        },
        data() {
            return {
                productImages: null
            }
        },
        methods: {
            changeAttribute(superAttributeLabel, option) {
                let children = JSON.parse(this.product).children;
                if(superAttributeLabel.toLowerCase() === 'size') return

                Object.keys(children).forEach(key => {
                    Object.keys(children[key]).forEach(keyAttribute => {
                        if(keyAttribute === superAttributeLabel.toLowerCase() && children[key][keyAttribute] == option) {
                            this.productImages = children[key].images;
                        }
                    })
                })
            }
        }
    }
</script>
