import { StorageSerializers, asyncComputed, useLocalStorage, useMemoize } from '@vueuse/core'
import { computed, watch } from 'vue'
import { mask, clearMask } from './useMask'

const cartStorage = useLocalStorage('cart', {}, { serializer: StorageSerializers.object })
let age = 0

export const refresh = async function (force = false) {
    if (!mask.value) {
        cartStorage.value = {}
        return false
    }

    if (!force && Date.now() - age < 5000) {
        // The latest cart info is 5 seconds old, we do not need to refresh.
        return
    }

    age = Date.now()

    try {
        let response = await window.magentoGraphQL(
            `query getCart($cart_id: String!) { cart (cart_id: $cart_id) { ${config.queries.cart} } }`,
            { cart_id: mask.value },
        )

        cart.value = Object.values(response.data)[0]
    } catch (error) {
        console.error(error)
        Vue.prototype.checkResponseForExpiredCart(error)
    }
}

export const clear = async function () {
    await clearAddresses()
    await clearMask()
    await refresh()
}

export const clearAddresses = async function () {
    useLocalStorage('billing_address').value = null
    useLocalStorage('shipping_address').value = null
}

export const linkUserToCart = async function () {
    await window
        .magentoGraphQL(`mutation ($cart_id: String!) { assignCustomerToGuestCart (cart_id: $cart_id) { ${config.queries.cart} } }`, {
            cart_id: mask.value,
        })
        .then((response) => Vue.prototype.updateCart([], response))
}

export const fetchCustomerCart = async function () {
    await window
        .magentoGraphQL(`query { customerCart { ${config.queries.cart} } }`)
        .then((response) => Vue.prototype.updateCart([], response))
}

export const fetchAttributeValues = async function (attributes = []) {
    if (!attributes.length) {
        return { data: { customAttributeMetadata: { items: null } } }
    }

    return await window.magentoGraphQL(
        `
            query attributeValues($attributes: [AttributeInput!]!) {
                customAttributeMetadata(attributes: $attributes) {
                    items {
                        attribute_code
                        attribute_options {
                            label
                            value
                        }
                    }
                }
            }
        `,
        {
            attributes: attributes.map((attribute_code) => {
                return { attribute_code: attribute_code, entity_type: 'catalog_product' }
            }),
        },
    )
}

const fetchAttributeValuesMemo = useMemoize(fetchAttributeValues)

export const getAttributeValues = async function () {
    return await fetchAttributeValuesMemo(window.config.cart_attributes)
}

export const cart = computed({
    get() {
        if (!cartStorage.value?.id && mask.value) {
            refresh()
        }

        cartStorage.value.virtualItems = virtualItems
        cartStorage.value.hasOnlyVirtualItems = hasOnlyVirtualItems
        cartStorage.value.fixedProductTaxes = fixedProductTaxes

        return cartStorage.value
    },
    set(value) {
        getAttributeValues()
            .then((response) => {
                if (!response?.data?.customAttributeMetadata?.items) {
                    return
                }

                const mapping = Object.fromEntries(
                    response.data.customAttributeMetadata.items.map((attribute) => [
                        attribute.attribute_code,
                        Object.fromEntries(attribute.attribute_options.map((value) => [value.value, value.label])),
                    ]),
                )

                value.items = value.items.map((cartItem) => {
                    cartItem.product.attribute_values = {}

                    for (const key in mapping) {
                        cartItem.product.attribute_values[key] = cartItem.product[key]
                        if (cartItem.product.attribute_values[key] === null) {
                            continue
                        }

                        if (typeof cartItem.product.attribute_values[key] === 'string') {
                            cartItem.product.attribute_values[key] = cartItem.product.attribute_values[key].split(',')
                        }

                        if (typeof cartItem.product.attribute_values[key] !== 'object') {
                            cartItem.product.attribute_values[key] = [cartItem.product.attribute_values[key]]
                        }

                        cartItem.product.attribute_values[key] = cartItem.product.attribute_values[key].map(
                            (value) => mapping[key][value] || value,
                        )
                    }

                    return cartItem
                })
            })
            .finally(() => {
                cartStorage.value = value
                age = Date.now()

                if (value.id && value.id !== mask.value) {
                    // Linking user to cart will create a new mask, it will be returned in the id field.
                    mask.value = value.id
                }
            })
    },
})

export const virtualItems = computed(() => {
    return cart.value?.items?.filter((item) => item.product.type_id == 'downloadable')
})

export const hasOnlyVirtualItems = computed(() => {
    return cart.value.total_quantity === virtualItems.value.length
})

export const fixedProductTaxes = computed(() => {
    let taxes = {}
    cart.value?.items?.forEach((item) =>
        item.prices?.fixed_product_taxes?.forEach((tax) => (taxes[tax.label] = (taxes[tax.label] ?? 0) + tax.amount.value)),
    )
    return taxes
})

export const sidebarSegments = computed(() => {
    if(!cart) {
        return []
    }

    let segments = []

    segments.push({
        'title': 'Subtotal',
        'value_including_tax': cart.prices.subtotal_including_tax.value,
        'value_excluding_tax': cart.prices.subtotal_excluding_tax.value,
    })

    if (cart.shipping_addresses?.length  && cart.shipping_addresses[0]?.selected_shipping_method?.amount) {
        segments.push({
            'title': 'Shipping',
            'value_including_tax': cart.shipping_addresses[0].selected_shipping_method.price_incl_tax.value,
            'value_excluding_tax': cart.shipping_addresses[0].selected_shipping_method.price_excl_tax.value,
        })
    }

    if (cart.prices?.applied_taxes?.length && cart.prices?.applied_taxes[0]?.amount?.value > 0) {
        segments.push({
            'title': 'Tax',
            'value_including_tax': cart.prices.applied_taxes[0].amount.value,
            'value_excluding_tax': 0,
        })
    }

    if(cart.prices?.discounts) {
        cart.prices.discounts.forEach(discount => {
            segments.push({
                'title': discount.label,
                'value_including_tax': -discount.amount.value,
                'value_excluding_tax': -discount.amount.value,
            })
        });
    }
})

export default () => cart
