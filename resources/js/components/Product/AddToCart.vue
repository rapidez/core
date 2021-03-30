<script>
    import GetCart from './../Cart/mixins/GetCart'
    import InteractWithUser from './../User/mixins/InteractWithUser'

    export default {
        mixins: [GetCart, InteractWithUser],
        props: {
            product: {
                type: Object,
                default: function () {
                    if (config.product) {
                        return config.product
                    }

                    return {}
                }
            }
        },

        data: () => ({
            options: {},
            error: null,
            qty: 1,
        }),

        render() {
            return this.$scopedSlots.default({
                getValuesByCode: this.getValuesByCode,
                simpleProduct: this.simpleProduct,
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
                        sku: this.product.sku,
                        quote_id: localStorage.mask,
                        qty: this.qty,
                        product_option: this.productOptions
                    }
                }).then(async (response) => {
                    this.error = null
                    await this.refreshCart()
                    if (config.redirect_cart) {
                        Turbolinks.visit('/cart');
                    }
                }).catch((error) => {
                    if (error.response.status == 401) {
                        alert('Your session expired, please login again')
                        this.logout('/login')
                    }
                    this.error = error.response.data.message
                })
            },

            getValuesByCode: function (code) {
                if (!this.product[code].length) {
                    // Result is already a value => label Object.
                    return this.product[code]
                }

                // Get value label using the swatches.
                if (this.getSwatches().hasOwnProperty(code)) {
                    let swatchOptions = this.getSwatches()[code].options
                    let values = {}

                    Object.entries(this.product[code]).forEach(([key, val]) => {
                        if (swatchOptions[val]) {
                            values[val] = swatchOptions[val]['value']
                        }
                    })

                    return values
                }

                return _.invert(this.product[code])
            },

            getSwatches: function() {
                if (localStorage.swatches) {
                    return JSON.parse(localStorage.swatches)
                }

                return {}
            }
        },

        computed: {
            simpleProduct: function() {
                var product = this.product

                if (!product.super_attributes) {
                    return product
                }

                var simpleProducts = Object.values(product.children).filter(childProduct => {
                    let isMatching = true
                    Object.entries(this.options).forEach(([attributeId, valueId]) => {
                        var attributeCode = product.super_attributes[attributeId].code

                        if (Number(childProduct[attributeCode]) !== Number(valueId)) {
                            isMatching = false
                        }
                    })
                    return isMatching
                })

                if (Object.keys(this.product.children).length == simpleProducts.length) {
                    return product
                }

                if (simpleProducts.length) {
                    product = simpleProducts[0]
                }

                this.$root.$emit('productSuperAttributeChange', product)

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

                if (!this.product.super_attributes) {
                    return disabledOptions
                }

                Object.entries(this.product.super_attributes).forEach(([attributeId, attribute]) => {
                    disabledOptions[attribute.code] = []
                    valuesPerAttribute[attributeId] = []

                    // Fill list with products per attribute value
                    Object.entries(this.product.children).forEach(([productId, option]) => {
                        if (!option.in_stock) {
                            return
                        }

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
                                    // If there is no product that intersects for this attribute value
                                    // there will be no product available for this attribute value
                                    if (_.intersection(productsPerValue[selectedValueId], products).length <= 0) {
                                        var attributeCode = this.product.super_attributes[attributeId2].code
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
