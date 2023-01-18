<script>
    import GetCart from './../Cart/mixins/GetCart'
    import InteractWithUser from './../User/mixins/InteractWithUser'

    export default {
        mixins: [GetCart, InteractWithUser],
        props: {
            product: {
                type: Object,
                default: () => config.product || {}
            },
            defaultQty: {
                type: Number,
                default: 1
            },
            addedDuration: {
                type: Number,
                default: 3000,
            },
            notifySuccess: {
                type: Boolean,
                default: false,
            },
            notifyError: {
                type: Boolean,
                default: true,
            },
            callback: {
                type: Function,
            },
        },

        data: () => ({
            qty: 1,
            options: {},
            error: null,

            adding: false,
            added: false,
        }),

        mounted() {
            this.qty = this.defaultQty
        },

        render() {
            return this.$scopedSlots.default({
                getOptions: this.getOptions,
                simpleProduct: this.simpleProduct,
                disabledOptions: this.disabledOptions,
                changeQty: this.changeQty,
                options: this.options,
                error: this.error,
                add: this.add,
                qty: this.qty,

                adding: this.adding,
                added: this.added,
            })
        },

        methods: {
            changeQty(event) {
                this.qty = event.target.value
            },

            async add() {
                if ('children' in this.product
                    && Object.values(this.product.children).length
                    && window.location.pathname !== this.product.url
                    && !config.show_swatches) {
                    Turbo.visit(this.product.url)
                    return;
                }

                this.added = false
                this.adding = true
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
                    this.added = true
                    setTimeout(() => { this.added = false }, this.addedDuration)
                    if (this.callback) {
                        await this.callback(this.product, this.qty)
                    }
                    this.$root.$emit('cart-add', {
                        product: this.product,
                        qty: this.qty,
                    })
                    if (this.notifySuccess) {
                        Notify(this.product.name + ' ' + window.config.translations.cart.add, 'success', [], '/cart')
                    }
                    if (config.redirect_cart) {
                        Turbo.visit('/cart')
                    }
                }).catch((error) => {
                    if (error.response.status == 401) {
                        Notify(window.config.translations.errors.session_expired, 'error', error.response.data?.parameters)
                        this.logout('/login')
                    }

                    if (this.expiredCartCheck(error)) {
                        return
                    }

                    if (this.notifyError) {
                        Notify(error.response.data.message, 'error', error.response.data?.parameters)
                    }

                    this.error = error.response.data.message
                }).then(() => {
                    this.adding = false
                })
            },

            getOptions: function (superAttributeCode) {
                if (this.$root.swatches?.hasOwnProperty(superAttributeCode)) {
                    let swatchOptions = this.$root.swatches[superAttributeCode].options
                    let values = {}

                    Object.entries(this.product[superAttributeCode]).forEach(([key, val]) => {
                        if (swatchOptions[val]) {
                            values[val] = swatchOptions[val]
                        }
                    })

                    return values
                }

                return {}
            },
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

                this.$root.$emit('product-super-attribute-change', product)

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
                                    if (window.intersection(productsPerValue[selectedValueId], products).length <= 0) {
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
