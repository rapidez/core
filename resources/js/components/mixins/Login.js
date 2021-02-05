export default {
    methods: {
        async loginIfSuccesful(params) {
            let query = "mutation { generateCustomerToken( email: \""+params.changes.email+"\" password: \""+params.changes.password+"\" ) { token  } }"
            let response = await this.doGraphqlMutation(query)

            localStorage.token = response.data.data.generateCustomerToken.token
            window.magentoUser.defaults.headers.common['Authorization'] = `Bearer ${localStorage.token}`;
            await this.refreshUser()

            if (this.$root.cart) {
                await this.linkUserToCart()
                localStorage.mask = this.$root.cart.entity_id
            } else {
                await this.refreshCart()
            }

        }
    }
}