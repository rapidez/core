import { StorageSerializers, asyncComputed, useLocalStorage, useMemoize } from '@vueuse/core'
import { computed, ref, watch } from 'vue'
import { GraphQLError } from '../fetch'
import { mask, clearMask } from './useMask'
import { user } from './useUser'

const cartStorage = useLocalStorage('cart', {}, { serializer: StorageSerializers.object })
let age = 0
let currentRefresh = null

export const refresh = async function (force = false) {
    if (!mask.value) {
        cartStorage.value = {}
        return false
    }

    if (currentRefresh) {
        console.debug('Refresh canceled, request already in progress...')
        return currentRefresh
    }

    if (!force && Date.now() - age < 5000) {
        // The latest cart info is 5 seconds old, we do not need to refresh.
        return
    }

    age = Date.now()

    return (currentRefresh = (async function () {
        try {
            let response = await window.magentoGraphQL(
                `query getCart($cart_id: String!) { cart (cart_id: $cart_id) { ...cart } }

                ` + config.fragments.cart,
                { cart_id: mask.value },
            )

            Vue.prototype.updateCart([], response)
        } catch (error) {
            console.error(error)
            GraphQLError.prototype.isPrototypeOf(error) && Vue.prototype.checkResponseForExpiredCart({}, await error?.response?.json())

            return false
        }
    })().finally(() => {
        currentRefresh = null
    }))
}

export const clear = async function () {
    await clearMask()
    await refresh()
    await clearAddresses()
}

export const clearAddresses = async function () {
    useLocalStorage('billing_address').value = null
    useLocalStorage('shipping_address').value = null
}

export const setGuestEmailOnCart = async function (email) {
    await window
        .magentoGraphQL(config.queries.setGuestEmailOnCart, {
            cart_id: mask.value,
            email: email,
        })
        .then((response) => Vue.prototype.updateCart([], response))
}

export const linkUserToCart = async function () {
    await window
        .magentoGraphQL(
            `mutation ($cart_id: String!) { assignCustomerToGuestCart (cart_id: $cart_id) { ...cart } }

            ` + config.fragments.cart,
            {
                cart_id: mask.value,
            },
        )
        .then((response) => Vue.prototype.updateCart([], response))
}

export const fetchCustomerCart = async function () {
    await window
        .magentoGraphQL(
            `query { customerCart { ...cart } }

            ` + config.fragments.cart,
        )
        .then((response) => Vue.prototype.updateCart([], response))
}

export const fetchGuestCart = async function () {
    await window
        .magentoGraphQL(
            `mutation { createGuestCart { cart { ...cart } } }

            ` + config.fragments.cart,
        )
        .then((response) => Vue.prototype.updateCart([], response))
}

export const fetchCart = async function () {
    if (user.value.is_logged_in) {
        await fetchCustomerCart()

        return
    }

    await fetchGuestCart()
}

export const fetchAttributeValues = async function (attributes = []) {
    if (!attributes.length) {
        return { data: { customAttributeMetadataV2: { items: null } } }
    }

    return await window.magentoGraphQL(
        `
            query attributeValues($attributes: [AttributeInput!]!) {
                customAttributeMetadataV2(attributes: $attributes) {
                    items {
                        code
                        options {
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

function areAddressesSame(address1, address2) {
    const fieldsToCompare = ['city', 'postcode', 'company', 'firstname', 'lastname', 'telephone']

    return (
        fieldsToCompare.every((field) => address1?.[field] === address2?.[field]) &&
        [0, 1, 2].every((key) => address1?.street?.[key] === address2?.street?.[key])
    )
}

function addCustomerAddressId(address) {
    // TODO: Remove if https://github.com/magento/magento2/pull/38909 is merged
    if (address?.customer_address_id || address === null) {
        return address
    }
    const customerAddress = user.value?.addresses?.find((customerAddress) => areAddressesSame(customerAddress, address))
    address.customer_address_id = address.customer_address_id || customerAddress?.id

    return address
}

export const checkAvailability = function (cartItem) {
    // Here we polyfill the is_available field. We need to do this
    // because the default is_available field supported by Magento
    // always returns true, even when a product is out of stock. This
    // should be fixed in the next Magento release, reference: https://github.com/magento/magento2/blame/2.4-develop/app/code/Magento/QuoteGraphQl/Model/CartItem/ProductStock.php#L61
    if ('stock_item' in cartItem.product && 'in_stock' in cartItem.product.stock_item && cartItem.product.stock_item.in_stock !== null) {
        return cartItem.product.stock_item.in_stock
    }

    // Without the use of compadre the in stock check can't be
    // done. We will need to always allow users to go on to
    // the checkout.
    return true
}

export const cart = computed({
    get() {
        if (!cartStorage.value?.id && mask.value) {
            refresh()
        }

        cartStorage.value.fixedProductTaxes = fixedProductTaxes
        cartStorage.value.taxTotal = taxTotal

        return cartStorage.value
    },
    set(value) {
        value.shipping_addresses = value.shipping_addresses?.map(addCustomerAddressId)
        if (value.billing_address !== null) {
            value.billing_address = addCustomerAddressId(value.billing_address)
            // TODO: Remove if https://github.com/magento/magento2/pull/38970 is merged
            value.billing_address.same_as_shipping = areAddressesSame(value.shipping_addresses[0], value.billing_address)
        }

        if (value.id && value.id !== mask.value) {
            // Linking user to cart will create a new mask, it will be returned in the id field.
            mask.value = value.id
        }

        getAttributeValues()
            .then((response) => {
                if (!response?.data?.customAttributeMetadataV2?.items) {
                    value.items = value.items.map((item) => {
                        item.is_available = checkAvailability(item)

                        return item
                    })

                    return
                }

                const mapping = Object.fromEntries(
                    response.data.customAttributeMetadataV2.items.map((attribute) => [
                        attribute.code,
                        Object.fromEntries(attribute.options.map((value) => [value.value, value.label])),
                    ]),
                )

                value.items = value.items.map((cartItem) => {
                    cartItem.is_available = checkAvailability(cartItem)
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
            })
    },
})

export const fixedProductTaxes = computed(() => {
    let taxes = {}
    // Note: Magento does internal rounding before multiplying by the quantity, so we actually don't lose any precision here by using the rounded tax amount.
    cart.value?.items?.forEach((item) =>
        item.prices?.fixed_product_taxes?.forEach((tax) => (taxes[tax.label] = (taxes[tax.label] ?? 0) + tax.amount.value * item.quantity)),
    )
    return taxes
})

export const taxTotal = computed(() => {
    if (!cart?.value?.prices?.applied_taxes?.length) {
        return 0
    }

    return cart.value.prices.applied_taxes.reduce((sum, tax) => sum + tax.amount.value, 0)
})

watch(mask, refresh)
if (cartStorage.value?.id && !mask.value) {
    clear()
}

export default () => cart
