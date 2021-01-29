<script>
    import GetCart from './../Cart/mixins/GetCart'
    import InteractWithUser from './../User/mixins/InteractWithUser'

    export default {
        mixins: [GetCart, InteractWithUser],

        data: () => ({
            options: {},
            error: null,
            qty: 1,
        }),

        render() {
            return this.$scopedSlots.default({
                price: this.simpleProduct.price,
                disabledOptions: this.disabledOptions,
                changeQty: this.changeQty,
                options: this.options,
                error: this.error,
                add: this.add,
                qty: this.qty,
            })
        },

        methods: {
            changeQty(event) {
                this.qty = event.target.value
            },

            async add() {
                await this.getMask()

                this.magentoCart('post', 'items', {
                    cartItem: {
                        sku: config.product.sku,
                        quote_id: localStorage.getItem('mask'),
                        qty: this.qty,
                        product_option: this.productOptions
                    }
                }).then((response) => {
                    this.refreshCart()
                    this.error = null
                }).catch((error) => {
                    if (error.response.status == 401) {
                        alert('Your session expired, please login again')
                        this.logout('/login')
                    }
                    this.error = error.response.data.message
                })
            },
        },

        computed: {
            simpleProduct: function() {
                var product = config.product

                var simpleProducts = Object.values(config.product.children).filter(childProduct => {
                    let isMatching = true
                    Object.entries(this.options).forEach(([attributeId, valueId]) => {
                        var attributeCode = config.product.super_attributes[attributeId].code

                        if (Number(childProduct[attributeCode]) !== Number(valueId)) {
                            isMatching = false
                        }
                    })
                    return isMatching
                })

                if (simpleProducts.length) {
                    product = simpleProducts[0]
                }

                return product
            },


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
                var valuesPerAttribute = {};

                if (!config.product.super_attributes) {
                    return disabledOptions
                }

                Object.entries(config.product.super_attributes).forEach(([attributeId, attribute]) => {
                    disabledOptions[attribute.code] = []
                    valuesPerAttribute[attributeId] = []

                    // Fill list with products per attribute value
                    Object.entries(config.product.children).forEach(([productId, option]) => {
                        if (!valuesPerAttribute[attributeId][option[attribute.code]]) {
                            valuesPerAttribute[attributeId][option[attribute.code]] = []
                        }

                        valuesPerAttribute[attributeId][option[attribute.code]].push(productId)
                    })
                })

                // Here we cross reference the attributes with each other
                // keeping in mind the products we have with the current
                // selected attribute values.
                Object.entries(valuesPerAttribute).forEach(([attributeId, productsPerValue]) => {
                    Object.entries(valuesPerAttribute).forEach(([attributeId2, productsPerValue2]) => {
                        if (attributeId !== attributeId2) {
                            var selectedValueId = this.options[attributeId]
                            if (selectedValueId) {
                                Object.entries(productsPerValue2).forEach(([valueId, products]) => {
                                    var intersects = productsPerValue[selectedValueId].filter(value => products.includes(value))
                                    // If there is no product that intersects for this attribute value
                                    // there will be no product available for this attribute value
                                    if (intersects.length <= 0) {
                                        var attributeCode = config.product.super_attributes[attributeId2].code
                                        disabledOptions[attributeCode].push(valueId)
                                    }
                                })
                            }
                        }
                    })
                })

                return disabledOptions
            }
        }
    }
</script>
