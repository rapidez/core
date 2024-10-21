import { StorageSerializers, useLocalStorage } from '@vueuse/core'
import { user } from './useUser'

export const order = useLocalStorage('order', {}, { serializer: StorageSerializers.object })

export const refresh = async function () {
    if (order.value?.number && user.value.is_logged_in) {
        return await loadCustomerByNumber(order.value?.number)
            .then(() => true)
            .catch(() => false)
    }

    if (order.value?.token && !user.value.is_logged_in) {
        return await loadGuestByToken(order.value?.token)
            .then(() => true)
            .catch(() => false)
    }

    return false
}

export const clear = async function () {
    order.value = {}
}

export async function loadCustomerByNumber(number) {
    await window
        .magentoGraphQL(
            `query customerOrder($number: String!) {
                customer {
                    orders(filter: { number: { eq: $number } }) {
                        items {
                            ...orderV2
                        }
                    }
                }
            }

        ` + config.fragments.orderV2,
            {
                number: number,
            },
        )
        .then(async (response) => await fillFromGraphqlResponse([], { data: response?.data?.customer?.orders?.items }))

    return order.value
}

export async function loadGuestByToken(token) {
    await window
        .magentoGraphQL(
            `query guestOrderByToken($token: String!) {
                guestOrderByToken(input: {token: $token}) {
                    ...orderV2
                }
            }

        ` + config.fragments.orderV2,
            {
                token: token,
            },
        )
        .then(async (response) => await fillFromGraphqlResponse([], response))

    return order.value
}

export async function loadGuestByCredentials(orderNumber, email, postcode) {
    await window
        .magentoGraphQL(
            `query guestOrder($email: String!, $number: String!, $postcode: String!) {
                guestOrder(input: {email: $email, number: $number, postcode: $postcode}) {
                    ...orderV2
                }
            }

        ` + config.fragments.orderV2,
            {
                number: orderNumber,
                email: email,
                postcode: postcode,
            },
        )
        .then(async (response) => await fillFromGraphqlResponse([], response))

    return order.value
}

export async function fillFromGraphqlResponse(data, response) {
    if (!response?.data) {
        return response?.data
    }

    order.value = 'orderV2' in Object.values(response.data)[0] ? Object.values(response.data)[0].orderV2 : Object.values(response.data)[0]

    return response.data
}

export default () => order
