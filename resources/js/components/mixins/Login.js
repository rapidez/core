export default {
    methods: {
        async loginIfSuccesful(params) {
            let query = "mutation { generateCustomerToken( email: \""+params.changes.email+"\" password: \""+params.changes.password+"\" ) { token  } }"
            let response = await this.doGraphqlRequest(query)
            localStorage.token = response.data.data.generateCustomerToken.token
            window.magentoUser.defaults.headers.common['Authorization'] = `Bearer ${localStorage.token}`;
            await this.refreshUser()
        },
        async checkPassword(params) {
            if(params.passwordConfirm !== this.changes.password) {
                alert("Passwords Don't match")
                return false
            }

            return true
        }
    }
}
