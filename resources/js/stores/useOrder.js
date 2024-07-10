import { StorageSerializers, useLocalStorage } from '@vueuse/core'
import { user } from './useUser';

export const order = useLocalStorage('order', {}, { serializer: StorageSerializers.object })

export const refresh = async function (force = false) {
    if (order.value?.token || user.is_logged_in) {
        return await loadGuestByToken(order.value?.token).then(() => true).catch(() => false);
    }

    return false;
}

export const clear = async function () {
    order.value = {}
}

export async function loadGuestByToken(token) {
    await window.magentoGraphQL(config.queries.orderV2 +
        `

            query guestOrderByToken($token: String!) {
                guestOrderByToken(input: {token: $token}) {
                    ...orderV2
                }
            }
        `,
        {
            token: token,
        },
    ).then(async (response) => await fillFromGraphqlResponse([], response));

    return order.value
}

export async function loadGuestByCredentials(orderNumber, email, postcode) {
    await window.magentoGraphQL(config.queries.cart +
        `

            query guestOrder($email: String!, $number: String!, $postcode: String!) {
                guestOrder(input: {email: $email, number: $number, postcode: $postcode}) {
                    ...order
                }
            }
        `,
        {
            number: orderNumber,
            email: email,
            postcode: postcode,
        },
    ).then(async (response) => await fillFromGraphqlResponse([], response));

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
