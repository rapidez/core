<script>
    import InteractWithUser from './../User/mixins/InteractWithUser'

    export default {
        mixins: [InteractWithUser],

        data: () => ({
            firstname: '',
            lastname: '',
            email: '',
            password: '',
            password_confirmation: '',
        }),

        render() {
            return this.$scopedSlots.default({
                email: this.email,
                password: this.password,
                password_confirmation: this.password_confirmation,
                firstname: this.firstname,
                lastname: this.lastname,
                go: this.go,
                inputChange: this.inputChange,
            })
        },

        created() {
            this.refreshUser(false)
            if (this.$root.user) {
                Turbolinks.visit('/account')
            }
        },

        methods: {
            async go() {
                if (!await this.validate()) {
                    return;
                }

                let responseData = await this.createCustomer(this.firstname, this.lastname, this.email, this.password)
                if (!responseData) {
                    return
                }

                let self = this
                this.login(this.email, this.password, async () => {
                    await self.refreshUser(true)
                    Turbolinks.visit('/account')
                });
            },

            async validate() {
                if (!this.email || !this.password) {
                    alert('You did not specify an email or password')
                    return false
                }

                let response = await this.checkEmailAvailability()
                if (!response.data) {
                    alert('This email is already in use')
                    return false
                }

                if (!this.firstname || !this.lastname) {
                    alert('You did not specify a firstname or lastname')
                    return false
                }

                if (this.password != this.password_confirmation) {
                    alert('Please make sure your password matches')
                    return false
                }

                return true
            },

            async checkEmailAvailability() {
                return magento.post('customers/isEmailAvailable', {
                    customerEmail: this.email
                })
            },

            inputChange(e) {
                this[e.target.id] = e.target.value
            },
        }
    }
</script>
