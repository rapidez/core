export default {
    methods: {
        checkPassword(params) {
            if(params.passwordConfirm !== this.changes.password) {
                alert("Passwords Don't match")
                return false
            }

            return true
        }
    }
}