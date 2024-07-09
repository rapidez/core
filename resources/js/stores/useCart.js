import { StorageSerializers, asyncComputed, useLocalStorage, useMemoize } from '@vueuse/core'
import { computed, watch } from 'vue'
import { mask, clearMask } from './useMask'
import { user } from './useUser'

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
        let response = await window.magentoGraphQL(config.queries.cart +
            `

            query getCart($cart_id: String!) { cart (cart_id: $cart_id) { ...cart } }`,
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

export const setGuestEmailOnCart = async function (email) {
    await window.magentoGraphQL(config.queries.setGuestEmailOnCart, {
        cart_id: mask.value,
        email: email
    })
    .then((response) => Vue.prototype.updateCart([], response))
}

export const linkUserToCart = async function () {
    await window
        .magentoGraphQL(config.queries.cart +
            `

            mutation ($cart_id: String!) { assignCustomerToGuestCart (cart_id: $cart_id) { ...cart } }`, {
            cart_id: mask.value,
        })
        .then((response) => Vue.prototype.updateCart([], response))
}

export const fetchCustomerCart = async function () {
    await window
        .magentoGraphQL(config.queries.cart +
            `

            query { customerCart { ...cart } }`)
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

function areAddressesSame(address1, address2) {
    const fieldsToCompare = [
        'city',
        'postcode',
        'company',
        'firstname',
        'lastname',
        'telephone'
    ];

    return fieldsToCompare.every((field) => address1[field] === address2[field]) && [0,1,2].every((key) => address1?.street[key] === address2?.street[key])
}

function addCustomerAddressId(address) {
    if (address?.customer_address_id) {
        return address
    }
    const customerAddress = user.value?.addresses?.find((customerAddress) => areAddressesSame(customerAddress, address))
    address.customer_address_id = address.customer_address_id || customerAddress?.id

    return address
}

export const cart = computed({
    get() {
        if (!cartStorage.value?.id && mask.value) {
            refresh()
        }

        cartStorage.value.virtualItems = virtualItems
        cartStorage.value.hasOnlyVirtualItems = hasOnlyVirtualItems

        return cartStorage.value
    },
    set(value) {
        value.shipping_addresses = value.shipping_addresses?.map(addCustomerAddressId)
        value.billing_address = addCustomerAddressId(value.billing_address)
        value.billing_address.same_as_shipping = areAddressesSame(value.shipping_addresses[0], value.billing_address)
        cartStorage.value = value
        age = Date.now()

        if (value.id && value.id !== mask.value) {
            // Linking user to cart will create a new mask, it will be returned in the id field.
            mask.value = value.id
        }
    },
})

export const virtualItems = computed(() => {
    return cart.value?.items?.filter((item) => item.product.type_id == 'downloadable')
})

export const hasOnlyVirtualItems = computed(() => {
    return cart.value.total_quantity === virtualItems.value.length
})

export default () => cart
