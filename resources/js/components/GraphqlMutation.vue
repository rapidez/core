<script>
    import InteractWithUser from './User/mixins/InteractWithUser'

    export default {
        mixins: [InteractWithUser],

        props: {
            query: {
                type: String,
                required: true,
            },
            variables: {
                type: Object,
                default: () => ({}),
            },
            changes: {
                type: Object,
                default: () => ({}),
            },
            redirect: {
                type: String,
                default: '',
            },
            alert: {
                type: Boolean,
                default: true,
            },
            clear: {
                type: Boolean,
                default: false,
            },
            notify: {
                type: Object,
                default: () => ({
                    message: '',
                    type: 'success'
                })
            },
            callback: {
                type: Function,
            },
            mutateEvent: {
                type: String,
                required: false
            },
            recaptcha: {
                type: Boolean,
                default: false,
            },
        },

        data: () => ({
            error: false,
            mutated: false,
            data: {},
        }),

        render() {
            return this.$scopedSlots.default({
                changes: this.changes,
                mutate: this.mutate,
                mutated: this.mutated,
                error: this.error,
                variables: this.data,
            })
        },

        created() {
            this.data = this.variables
        },

        watch: {
            variables: function (variables) {
                this.data = variables
            }
        },

        mounted() {
            if (this.mutateEvent) {
                window.app.$on(this.mutateEvent, () => {
                    this.mutate()
                })
            }
        },

        methods: {
            async mutate() {
                delete this.changes.id
                this.error = false

                try {
                    let options = { headers: {} }

                    if (this.$root.user) {
                        options['headers']['Authorization'] = `Bearer ${localStorage.token}`
                    }

                    if (this.recaptcha) {
                        options['headers']['X-ReCaptcha'] = await this.getReCaptchaToken()
                    }

                    let response = await axios.post(config.magento_url + '/graphql', {
                        query: this.query.replace('changes', this.queryfy(this.changes)),
                        variables: this.data,
                    }, options)

                    if (response.data.errors) {
                        if (response.data.errors[0]?.extensions?.category == 'graphql-authorization') {
                            this.logout('/login')
                            return
                        }

                        this.error = response.data.errors[0].message
                        if (this.alert) {
                            Notify(response.data.errors[0].message, 'error')
                        }
                        return
                    }

                    if (this.callback) {
                        await this.callback(this.changes, this.data, response)
                    }

                    if (this.clear) {
                        this.changes = {}
                        this.data = {}
                    }

                    var self = this
                    self.mutated = true
                    setTimeout(function() {
                        self.mutated = false
                    }, 2500);

                    if (!this.redirect && this.notify.message) {
                        Notify(this.notify.message, this.notify.type ?? 'success')
                    }

                    if (this.redirect) {
                        if (this.notify.message) {
                            setTimeout(() => {
                                Notify(this.notify.message, this.notify.type ?? 'success')
                            }, 1500)
                        }

                        Turbolinks.visit(this.redirect)
                    }
                } catch (e) {
                    Notify(window.config.translations.errors.wrong, 'warning')
                }
            },

            getReCaptchaToken() {
                return new Promise((res, rej) => {
                    grecaptcha.ready(function() {
                        grecaptcha.execute(window.config.recaptcha, { action: 'submit' }).then(function(token) {
                            return res(token)
                        })
                    })
                })
            },

            // Credits: https://stackoverflow.com/a/54262737/622945
            queryfy (obj, key = null) {
                if (typeof obj === 'number') {
                    return obj
                }

                if (obj === null) {
                    return JSON.stringify('')
                }

                if (typeof obj !== 'object' || Array.isArray(obj)) {
                    return key == 'country_code' ? obj : JSON.stringify(obj)
                }

                let props = Object.keys(obj).map(key =>
                     `${key}:${this.queryfy(obj[key], key)}`
                ).join(',')

                return Object.keys(this.changes).length === 1
                    ? props
                    : `{${props}}`
            }
        }
    }
</script>
