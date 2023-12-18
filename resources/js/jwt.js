export const isJwt = function (token) {
    return !!token.match(/^(?:[\w-]*\.){2}[\w-]*$/)
}

// NOTE: we do not and can not validate the token, never assume this is truth.
export const decode = function (token) {
    if (!isJwt(token)) {
        return null
    }
    const base64Url = token.split('.')[1]
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/')
    const jsonPayload = decodeURIComponent(
        window
            .atob(base64)
            .split('')
            .map(function (c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
            })
            .join(''),
    )

    let jwt = JSON.parse(jsonPayload)
    jwt.expDate = new Date(jwt.exp * 1000);
    jwt.isExpired = () => this.expDate < new Date()

    return jwt
}

export default { isJwt: isJwt, decode: decode }
