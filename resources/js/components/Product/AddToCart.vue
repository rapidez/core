<script>
import { GraphQLError } from '../../fetch'
import { mask, refreshMask } from '../../stores/useMask'

export default {
    inject: {
        instantSearchInstance: {
            from: '$_ais_instantSearchInstance',
            default: () => {
                return {
                    helper: {
                        state: {
                            disjunctiveFacetsRefinements: {},
                        },
                    },
                }
            },
        },
    },

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
        customSelectedOptions: {},
        error: null,

        adding: false,
        added: false,

        price: 0,
        specialPrice: 0,
    }),

    mounted() {
        this.qty = this.defaultQty
        this.calculatePrices()

        this.$nextTick(() => {
            this.setDefaultOptions()
            this.setDefaultCustomSelectedOptions()
            this.setOptionsFromRefinements()
        })
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    methods: {
        async add() {
            if (this.shouldRedirectToProduct) {
                Turbo.visit(window.url(this.product.url))
                return
            }

            this.added = false
            this.adding = true
            this.error = null

            if (!mask.value) {
                await refreshMask()
            }

            try {
                let response = await window.magentoGraphQL(
                    `mutation (
                        $cartId: String!,
                        $sku: String!,
                        $quantity: Float!,
                        $selected_options: [ID!],
                        $entered_options: [EnteredOptionInput]
                    ) { addProductsToCart(cartId: $cartId, cartItems: [{
                        sku: $sku,
                        quantity: $quantity,
                        selected_options: $selected_options,
                        entered_options: $entered_options
                    }]) { cart { ...cart } user_errors { code message } } }

                    ` + config.fragments.cart,
                    {
                        sku: this.product.sku,
                        cartId: mask.value,
                        quantity: this.qty,
                        selected_options: this.selectedOptions,
                        entered_options: this.enteredOptions,
                    },
                )

                // If there are user errors we may still get a newly updated cart back.
                await this.updateCart({}, response)

                if (response.data.addProductsToCart.user_errors.length) {
                    throw new Error(response.data.addProductsToCart.user_errors[0].message)
                }

                this.added = true
                setTimeout(() => {
                    this.added = false
                }, this.addedDuration)

                if (this.callback) {
                    await this.callback(this.product, this.qty)
                }

                this.$root.$emit('cart-add', { product: this.product, qty: this.qty })

                if (this.notifySuccess) {
                    Notify(this.product.name + ' ' + window.config.translations.cart.add, 'success', [], window.url('/cart'))
                }

                if (config.redirect_cart) {
                    Turbo.visit(window.url('/cart'))
                }
            } catch (error) {
                console.error(error)
                this.error = error.message

                if (this.notifyError) {
                    Notify(error.message, 'error')
                }

                if (error?.response) {
                    const responseData = await error.response.json()
                    if (GraphQLError.prototype.isPrototypeOf(error) && !(await this.checkResponseForExpiredCart({}, responseData))) {
                        // If there are errors we may still get a newly updated cart back.
                        await this.updateCart({}, responseData)
                    }
                }
            }

            this.adding = false
        },

        calculatePrices: function () {
            this.price = Math.round((parseFloat(this.simpleProduct.price) + this.priceAddition(this.simpleProduct.price)) * 100) / 100
            this.specialPrice =
                Math.round((parseFloat(this.simpleProduct.special_price) + this.priceAddition(this.simpleProduct.special_price)) * 100) /
                100
        },

        getOptions: function (superAttributeCode) {
            if (window.config.swatches.hasOwnProperty(superAttributeCode)) {
                let swatchOptions = window.config.swatches[superAttributeCode].options
                let values = {}

                Object.entries(this.product['super_' + superAttributeCode]).forEach(([key, val]) => {
                    let swatch = Object.values(swatchOptions).find((swatch) => swatch.value === val)
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

            if (!file) {
                return
            }

            let reader = new FileReader()
            reader.onerror = (error) => alert(error)
            reader.onload = () => {
                let [type, data] = reader.result.split(';', 4)

                if (!data) {
                    Vue.set(this.customOptions, optionId, undefined)
                    return
                }

                let value = {
                    base64_encoded_data: data.replace('base64,', ''),
                    type: type.replace('data:', ''),
                    name: file.name,
                }
                Vue.set(this.customOptions, optionId, JSON.stringify(value))
            }
            reader.readAsDataURL(file)
        },

        priceAddition: function (basePrice) {
            let addition = 0
            if (!this?.product?.options) {
                return addition
            }

            let optionEntries = Object.entries(this.customOptions)
            let selectedOptionEntries = Object.entries(this.customSelectedOptions)

            ;[...optionEntries, ...selectedOptionEntries].forEach(([key, vals]) => {
                ;[vals].flat().forEach((val) => {
                    if (!val) {
                        return
                    }

                    try {
                        let option = this.product.options.find((option) => option.option_id == key)
                        let optionPrice = option.price || option.values?.find((value) => value.option_type_id == val)?.price

                        if (optionPrice.price_type == 'fixed') {
                            addition += parseFloat(optionPrice.price)
                        } else {
                            addition += (parseFloat(basePrice) * parseFloat(optionPrice.price)) / 100
                        }
                    } catch (e) {
                        console.error('Price addition calcuation failed, prices may display incorrect!', this, e)
                    }
                })
            })

            return addition
        },
        async setDefaultOptions() {
            if (!window.config.add_to_cart?.auto_select_configurable_options || !window.config.product?.super_attributes) {
                return
            }
            // We do not loop and use the values of enabledOptions directly.
            // This is on purpose in order to force recalculations of enabledOptions to be considered.
            Object.keys(this.enabledOptions).map((attributeKey) => {
                Vue.set(this.options, attributeKey, this.enabledOptions[attributeKey].find(Boolean))
            })
        },
        async setDefaultCustomSelectedOptions() {
            if (!window.config.add_to_cart?.auto_select_product_options || !window.config.product?.options) {
                return
            }

            window.config.product.options.map((option) => {
                if (!option.is_require || !option.values) {
                    return
                }
                let value = option.values
                    .sort((aValue, bValue) => aValue.sort_order - bValue.sort_order)
                    .find((value) => value.in_stock)?.option_type_id
                if (!value) {
                    return
                }

                Vue.set(this.customSelectedOptions, option.option_id, value)
            })
        },
        async setOptionsFromRefinements() {
            Object.entries(this.refinementOptions).forEach(([key, availableValue]) => {
                if (!availableValue.length) {
                    return
                }

                Vue.set(this.options, key, availableValue[0])
            })
        },
    },

    computed: {
        currentThumbnail: function () {
            return this.simpleProduct?.thumbnail || this.simpleProduct?.images?.[0] || this.product?.thumbnail
        },

        shouldRedirectToProduct: function () {
            // Never redirect if we're already on the product page
            if (window.location.pathname === this.product.url) {
                return false
            }

            // Products with product options always have to be set on the product page
            if (this.product?.has_options) {
                return true
            }

            // Check if all super_attributes have an option selected
            return Object.keys(this.product?.super_attributes || {}).join(',') !== Object.keys(this.options).join(',')
        },

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

            if (Object.keys(this.product.children).length == simpleProducts.length && simpleProducts.length > 1) {
                return product
            }

            if (simpleProducts.length) {
                product = simpleProducts[0]
            }

            this.$root.$emit('product-super-attribute-change', product)

            return product
        },

        selectedOptions: function () {
            let selectedOptions = []

            Object.entries(this.options).forEach(([optionId, optionValue]) => {
                selectedOptions.push(btoa('configurable/' + optionId + '/' + optionValue))
            })

            Object.entries(this.customSelectedOptions).forEach(([optionId, optionValues]) => {
                ;[optionValues].flat().forEach((optionValue) => {
                    selectedOptions.push(btoa('custom-option/' + optionId + '/' + optionValue))
                })
            })

            return selectedOptions
        },

        enteredOptions: function () {
            let enteredOptions = []

            Object.entries(this.customOptions).forEach(([key, val]) => {
                enteredOptions.push({
                    uid: btoa('custom-option/' + key),
                    value: val,
                })
            })

            return enteredOptions
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
                    if (!valuesPerAttribute[attributeId][option[attribute.code]]) {
                        valuesPerAttribute[attributeId][option[attribute.code]] = []
                    }

                    if (!option.in_stock) {
                        if (Object.keys(this.product.super_attributes).length === 1) {
                            disabledOptions['super_' + attribute.code].push(option[attribute.code])
                        }

                        return
                    }

                    valuesPerAttribute[attributeId][option[attribute.code]].push(productId)
                })
            })

            // Here we cross reference the attributes with each other
            // keeping in mind the products we have with the current
            // selected attribute values. (see: https://github.com/rapidez/core/pull/7#issue-796718297)
            Object.entries(valuesPerAttribute).forEach(([attributeId, productsPerValue]) => {
                Object.entries(valuesPerAttribute).forEach(([attributeId2, productsPerValue2]) => {
                    if (attributeId === attributeId2) return
                    var selectedValueId = this.options[attributeId]
                    var attributeCode = this.product.super_attributes[attributeId2].code

                    Object.entries(productsPerValue2).forEach(([valueId, products]) => {
                        // If there is no product that intersects for this attribute value
                        // there will be no product available for this attribute value

                        if (
                            products.length &&
                            (!selectedValueId ||
                                !productsPerValue[selectedValueId] ||
                                productsPerValue[selectedValueId].some((val) => products.includes(val)))
                        ) {
                            return
                        }

                        disabledOptions['super_' + attributeCode].push(parseInt(valueId))
                    })
                })
            })

            return disabledOptions
        },
        enabledOptions: function () {
            let valuesPerAttribute = {}
            Object.entries(this.product.super_attributes).forEach(([attributeId, attribute]) => {
                valuesPerAttribute[attributeId] = []
                // Fill list with products per attribute value
                Object.entries(this.product.children).forEach(([productId, product]) => {
                    if (!product.in_stock || this.disabledOptions['super_' + attribute.code].includes(product.value)) {
                        return
                    }
                    valuesPerAttribute[attributeId].push(product[attribute.code])
                })
            })
            return valuesPerAttribute
        },

        superRefinements() {
            // Note that `disjunctiveFacetsRefinements` is only one of 5 total sets of refinements that gets exposed by the state
            // It looks like all super attributes will end up in there, so right now it's the only one we check
            let disjunctiveFacetsRefinements = this.instantSearchInstance.helper.state.disjunctiveFacetsRefinements
            return Object.fromEntries(
                Object.entries(disjunctiveFacetsRefinements)
                    .filter(([key, value]) => key.startsWith('super_') && value.length > 0)
                    .map(([key, value]) => [key.replace('super_', ''), value]),
            )
        },

        refinementOptions() {
            // Options per super attribute that match the current refinements.
            return Object.fromEntries(
                Object.entries(this.product?.super_attributes || {}).map(([index, attribute]) => {
                    return [
                        index,
                        (Object.entries(this.superRefinements).find(([key, value]) => key === attribute.code)?.[1] || []).filter((val) =>
                            this.enabledOptions[index].includes(val * 1),
                        ),
                    ] // Filter out disabled options
                }),
            )
        },
    },

    watch: {
        superRefinements: {
            handler(refinements) {
                this.setOptionsFromRefinements()
            },
            deep: true,
        },
        customOptions: {
            handler() {
                this.calculatePrices()
            },
            deep: true,
        },
        customSelectedOptions: {
            handler() {
                this.calculatePrices()
            },
            deep: true,
        },
        options: {
            handler() {
                this.calculatePrices()
            },
            deep: true,
        },
    },
}
</script>
