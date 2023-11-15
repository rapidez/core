<script>
import GetCart from './../Cart/mixins/GetCart'
import InteractWithUser from './../User/mixins/InteractWithUser'

export default {
    mixins: [GetCart, InteractWithUser],
    props: {
        product: {
            type: Object,
            default: () => config.product || {},
        },
        defaultQty: {
            type: Number,
            default: 1,
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
        customOptions: {},
        error: null,

        adding: false,
        added: false,

        price: 0,
        specialPrice: 0,
    }),

    mounted() {
        this.qty = this.defaultQty
        this.calculatePrices()
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    methods: {
        async add() {
            if (
                window.location.pathname !== this.product.url &&
                (this.product?.has_options ||
                    ('children' in this.product && Object.values(this.product.children).length && !config.show_swatches))
            ) {
                Turbo.visit(window.url(this.product.url))
                return
            }

            this.added = false
            this.adding = true
            await this.getMask()

            // TODO: Maybe make this generic? See: https://github.com/rapidez/core/pull/376
            // TODO: Check why configurable products are added as simple product. See: https://github.com/magento/devdocs/issues/9493
            axios.post(config.magento_url + '/graphql', {
                query: `mutation ($cartId: String!, $sku: String!, $parent_sku: String, $quantity: Float!) {
                    addProductsToCart(cartId: $cartId, cartItems: [
                        { sku: $sku, parent_sku: $parent_sku, quantity: $quantity }
                    ]) { cart { ` + config.queries.cart + ` } }
                }`,
                variables: {
                    sku: this.simpleProduct.sku,
                    parent_sku: this.product.sku,
                    cartId: localStorage.mask,
                    quantity: this.qty,
                    // TODO: implement: product_option: this.productOptions,
                }
            }).then(async (response) => {
                this.error = null
                await this.refreshCart({}, response)
                this.added = true
                setTimeout(() => {
                    this.added = false
                }, this.addedDuration)
                if (this.callback) {
                    await this.callback(this.product, this.qty)
                }
                this.$root.$emit('cart-add', {
                    product: this.product,
                    qty: this.qty,
                })
                if (this.notifySuccess) {
                    Notify(this.product.name + ' ' + window.config.translations.cart.add, 'success', [], window.url('/cart'))
                }
                if (config.redirect_cart) {
                    Turbo.visit(window.url('/cart'))
                }
            })
            .catch((error) => {
                if (error.response.status == 401) {
                    Notify(window.config.translations.errors.session_expired, 'error', error.response.data?.parameters)
                    this.logout(window.url('/login'))
                }

                if (this.expiredCartCheck(error)) {
                    return
                }

                if (this.notifyError) {
                    Notify(error.response.data.message, 'error', error.response.data?.parameters)
                }

                this.error = error.response.data.message
            })
            .then(() => {
                this.adding = false
            })
        },

        calculatePrices: function () {
            this.price = parseFloat(this.simpleProduct.price) + this.priceAddition(this.simpleProduct.price)
            this.specialPrice = parseFloat(this.simpleProduct.special_price) + this.priceAddition(this.simpleProduct.special_price)
        },

        getOptions: function (superAttributeCode) {
            if (this.$root.swatches.hasOwnProperty(superAttributeCode)) {
                let swatchOptions = this.$root.swatches[superAttributeCode].options
                let values = {}

                Object.entries(this.product['super_' + superAttributeCode]).forEach(([key, val]) => {
                    let swatch = swatchOptions.find((swatch) => swatch.value === val)
                    if (swatch) {
                        values[val] = swatch
                    }
                })

                return values
            }

            return {}
        },

        setCustomOptionFile(event, optionId) {
            let file = event.target.files[0]
            let reader = new FileReader()
            reader.onerror = (error) => alert(error)
            reader.onload = () => Vue.set(this.customOptions, optionId, 'FILE;' + file.name + ';' + reader.result)
            reader.readAsDataURL(file)
        },

        priceAddition: function (basePrice) {
            let addition = 0

            Object.entries(this.customOptions).forEach(([key, val]) => {
                if (!val) {
                    return
                }

                let option = this.product.options.find((option) => option.option_id == key)
                let optionPrice = ['drop_down', 'radio'].includes(option.type)
                    ? option.values.find((value) => value.option_type_id == val).price
                    : option.price

                if (optionPrice.price_type == 'fixed') {
                    addition += parseFloat(optionPrice.price)
                } else {
                    addition += (parseFloat(basePrice) * parseFloat(optionPrice.price)) / 100
                }
            })

            return addition
        },
    },

    computed: {
        simpleProduct: function () {
            var product = this.product

            if (!product.super_attributes) {
                return product
            }

            var simpleProducts = Object.values(product.children).filter((childProduct) => {
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
            let customOptions = []

            Object.entries(this.customOptions).forEach(([key, val]) => {
                if (typeof val === 'string' && val.startsWith('FILE;')) {
                    let [prefix, name, type, data] = val.split(';', 4)

                    if (!data) {
                        return
                    }

                    customOptions.push({
                        option_id: key,
                        option_value: 'file',
                        extension_attributes: {
                            file_info: {
                                base64_encoded_data: data.replace('base64,', ''),
                                type: type.replace('data:', ''),
                                name: name,
                            },
                        },
                    })

                    return
                }

                customOptions.push({
                    option_id: key,
                    option_value: val,
                })
            })

            return {
                extension_attributes: {
                    custom_options: customOptions,
                },
            }
        },

        disabledOptions: function () {
            var disabledOptions = {}
            var valuesPerAttribute = {}

            if (!this.product.super_attributes) {
                return disabledOptions
            }

            Object.entries(this.product.super_attributes).forEach(([attributeId, attribute]) => {
                disabledOptions['super_' + attribute.code] = []
                valuesPerAttribute[attributeId] = {}

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
                    if (attributeId === attributeId2) return
                    var selectedValueId = this.options[attributeId]
                    if (!selectedValueId) return
                    var attributeCode = this.product.super_attributes[attributeId2].code

                    Object.entries(productsPerValue2).forEach(([valueId, products]) => {
                        // If there is no product that intersects for this attribute value
                        // there will be no product available for this attribute value
                        if (!productsPerValue[selectedValueId] || productsPerValue[selectedValueId].some((val) => products.includes(val))) {
                            return
                        }

                        disabledOptions['super_' + attributeCode].push(valueId)
                    })
                })
            })

            return disabledOptions
        },
    },

    watch: {
        customOptions: {
            handler() {
                this.calculatePrices()
            },
            deep: true,
        },
    },
}
</script>
