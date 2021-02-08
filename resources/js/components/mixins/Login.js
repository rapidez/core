export default {
    methods: {
        async loginIfSuccesful(params) {
            let query = "mutation { generateCustomerToken( email: \""+params.changes.email+"\" password: \""+params.changes.password+"\" ) { token  } }"
            let response = await this.doGraphqlMutation(query)

            localStorage.token = response.data.data.generateCustomerToken.token
            window.magentoUser.defaults.headers.common['Authorization'] = `Bearer ${localStorage.token}`;
            let userResponse = await magentoUser.get('customers/me')
            localStorage.user = JSON.stringify(userResponse.data)
        },
    }
}