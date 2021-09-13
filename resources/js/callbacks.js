Vue.prototype.processCartData = async function (data) {
    return this.$root.cart = data.cart
}

Vue.prototype.refreshCart = async function () {
    return this.$root.$emit('refresh-cart')
}
