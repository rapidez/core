<script>
    import GetCart from './../Cart/mixins/GetCart'

    export default {
        mixins: [GetCart],

        data: () => ({
            options: {},
            error: null,
        }),

        render() {
            return this.$scopedSlots.default({
                disabledOptions: this.disabledOptions,
                options: this.options,
                error: this.error,
                add: this.add,
            })
        },

        methods: {
            async add() {
                await this.getMask()

                this.magentoCart('post', 'items', {
                    cartItem: {
                        sku: config.product.sku,
                        quote_id: localStorage.getItem('mask'),
                        qty: 1,
                        product_option: this.productOptions
                    }
                }).then((response) => {
                    this.refreshCart()
                    this.error = null
                }).catch((error) => {
                    this.error = error.response.data.message
                })
            },
        },

        computed: {
            productOptions: function () {
                let options = []
                Object.entries(this.options).forEach(([key, val]) => {
                    options.push({
                        option_id: key,
                        option_value: val,
                    });
                });
                return {
                    extension_attributes: {
                        configurable_item_options: options
                    }
                }
            },

            disabledOptions: function () {
                var disabledOptions = {};

                if (!config.product.super_attributes) {
                    return disabledOptions
                }

                Object.entries(config.product.super_attributes).forEach(([attributeId, attribute]) => {
                    disabledOptions[attribute.code] = []
                })

                // Some looping and filtering magic should be performed here to match
                // the children with the selected options and disabled options based
                // on which children are available. Have a look at the Magento demo
                // to see how it's working. When that's working the price also
                // needs to change based on the selection, first things first
                Object.entries(config.product.children).forEach(([productId, option]) => {
                    Object.entries(this.options).forEach(([attributeId, optionId]) => {
                        let attributeCode = config.product.super_attributes[attributeId].code
                        // disabledOptions[attributeCode].push(optionId)
                    })
                })

                return disabledOptions
            }
        }
    }
</script>
