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
        tierPrice: null,
        error: null,

        adding: false,
        added: false,
    }),

    mounted() {
        this.qty = this.defaultQty
    },

    render() {
        return this.$scopedSlots.default(Object.assign(this, { self: this }))
    },

    methods: {
        async add() {
            if (
                'children' in this.product &&
                Object.values(this.product.children).length &&
                window.location.pathname !== this.product.url &&
                !config.show_swatches
            ) {
                Turbo.visit(window.url(this.product.url))
                return
            }

            this.added = false
            this.adding = true
            await this.getMask()

            this.magentoCart('post', 'items', {
                cartItem: {
                    sku: this.product.sku,
                    quote_id: localStorage.mask,
                    qty: this.qty,
                    product_option: this.productOptions,
                },
            })
                .then(async (response) => {
                    this.error = null
                    await this.refreshCart()
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
            reader.onload = () => (this.customOptions[optionId] = 'FILE;' + file.name + ';' + reader.result)
            reader.readAsDataURL(file)
        },

        priceAddition: function (basePrice) {
            let addition = 0

            Object.entries(this.customOptions).forEach(([key, val]) => {
                if (!val) {
                    return
                }

                let option = this.product.options.find((option) => option.option_id == key)
                let optionPrice = ['drop_down'].includes(option.type)
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

        getTierPrice(qty) {
            qty = parseInt(qty)
            let best = null
            let bestQty = 0
            Object.entries(this.tierPrices).forEach(([id, tier]) => {
                if (tier.qty > bestQty && tier.qty <= qty) {
                    best = id
                    bestQty = tier.qty
                }
            })

            return best
        },

        tierPriceDiscount(price, tier) {
            let discount = tier ?? this.tierPrices[this.tierPrice] ?? null;
            if(!discount) {
                return price
            }

            if (discount.percentage_value > 0) {
                return price * (100 - discount.percentage_value) / 100
            }

            if (discount.value > 0) {
                return price - discount.value
            }

            return price
        },

        selectTier(id) {
            this.tierPrice = id

            let tierQty = this.tierPrices[this.tierPrice]?.qty ?? 1
            this.qty = parseInt(tierQty);
        }
    },

    watch: {
        qty() {
            this.tierPrice = this.getTierPrice(this.qty)
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

        tierPrices: function ()  {
            if (!this.product.tierprices || this.product.tierprices.length == 0) {
                return {}
            }

            let availableOptions = this.product.tierprices.filter(option =>
                option.all_groups ||
                option.customer_group_id == this.$root.user?.group_id
            )

            return Object.fromEntries(availableOptions.map(option => [
                option.value_id,
                {
                    qty: option.qty,
                    value: option.value,
                    percentage_value: option.percentage_value,
                    price: this.tierPriceDiscount(this.basePrice, option)
                }
            ]))
        },

        productOptions: function () {
            let options = []
            let customOptions = []

            Object.entries(this.options).forEach(([key, val]) => {
                options.push({
                    option_id: key,
                    option_value: val,
                })
            })

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
                    configurable_item_options: options,
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

        basePrice: function () {
            return parseFloat(this.simpleProduct.price) + this.priceAddition(this.simpleProduct.price)
        },

        baseSpecialPrice: function () {
            return parseFloat(this.simpleProduct.special_price) + this.priceAddition(this.simpleProduct.special_price)
        },

        price: function () {
            return this.tierPriceDiscount(this.basePrice)
        },

        specialPrice: function () {
            return this.tierPriceDiscount(this.baseSpecialPrice)
        },
    },
}
</script>
