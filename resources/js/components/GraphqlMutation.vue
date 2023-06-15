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
        watch: {
            type: Boolean,
            default: true,
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
                type: 'success',
            }),
        },
        callback: {
            type: Function,
        },
        beforeRequest: {
            type: Function,
        },
        mutateEvent: {
            type: String,
            required: false,
        },
        recaptcha: {
            type: Boolean,
            default: false,
        },
    },

    data: () => ({
        error: false,
        mutated: false,
        mutating: false,
        initialVariables: {},
        data: {},
    }),

    render() {
        return this.$scopedSlots.default({
            mutate: this.mutate,
            mutated: this.mutated,
            mutating: this.mutating,
            error: this.error,
            variables: this.data,
        })
    },

    created() {
        this.initialVariables = JSON.parse(JSON.stringify(this.variables))
        this.data = this.variables
    },

    watch: {
        variables: function (variables) {
            if (this.watch) {
                this.data = variables
            }
        },
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
            this.mutating = true
            this.error = false

            try {
                let options = { headers: {} }

                if (this.$root.loggedIn) {
                    options['headers']['Authorization'] = `Bearer ${localStorage.token}`
                }

                if (this.recaptcha) {
                    options['headers']['X-ReCaptcha'] = await this.getReCaptchaToken()
                }

                if (window.config.store_code) {
                    options['headers']['Store'] = window.config.store_code
                }

                let variables = this.data,
                    query = this.query

                if (this.beforeRequest) {
                    ;[query, variables, options] = await this.beforeRequest(this.query, this.variables, options)
                }

                let response = await axios.post(
                    config.magento_url + '/graphql',
                    {
                        query: query,
                        variables: variables,
                    },
                    options
                )

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
                    await this.callback(this.data, response)
                }

                if (this.clear) {
                    this.data = this.initialVariables
                }

                var self = this
                self.mutated = true
                setTimeout(function () {
                    self.mutated = false
                }, 2500)

                if (!this.redirect && this.notify.message) {
                    this.mutating = false
                    Notify(this.notify.message, this.notify.type ?? 'success')
                }

                if (this.redirect) {
                    if (this.notify.message) {
                        document.addEventListener(
                            'turbo:load',
                            () => {
                                Notify(this.notify.message, this.notify.type ?? 'success')
                            },
                            { once: true }
                        )
                    }
                    this.mutating = false
                    Turbo.visit(this.redirect)
                }
            } catch (e) {
                this.mutating = false
                Notify(window.config.translations.errors.wrong, 'warning')
            }
        },

        getReCaptchaToken() {
            return new Promise((res, rej) => {
                grecaptcha.ready(function () {
                    grecaptcha.execute(window.config.recaptcha, { action: 'submit' }).then(function (token) {
                        return res(token)
                    })
                })
            })
        },
    },
}
</script>
