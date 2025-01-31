<script>
import { GraphQLError, combiningGraphQL, magentoGraphQL } from '../fetch'

export default {
    props: {
        query: {
            type: String,
            required: true,
        },
        variables: {
            type: Object,
            default: () => ({}),
        },
        group: {
            // Group name for combining graphql queries, use "nameless" to join it with all others
            type: String,
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
        errorCallback: {
            type: Function,
        },
        store: {
            type: String,
            default: window.config.store_code,
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
            watch: this.watch,
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
            this.$nextTick(() =>
                window.app.$on(this.mutateEvent, () => {
                    this.mutate()
                }),
            )
        }
    },

    methods: {
        async mutate() {
            this.mutating = true
            this.error = false

            try {
                let options = { headers: { Store: this.store } }

                if (this.recaptcha) {
                    options['headers']['X-ReCaptcha'] = await this.getReCaptchaToken()
                }

                let variables = this.data,
                    query = this.query

                if (this.beforeRequest) {
                    ;[query, variables, options] = await this.beforeRequest(query, variables, options)
                }

                let response = await (
                    this.group ? combiningGraphQL(query, variables, options, this.group) : magentoGraphQL(query, variables, options)
                ).catch(async (error) => {
                    if (!GraphQLError.prototype.isPrototypeOf(error)) {
                        throw error
                    }

                    const errorResponse = await error.response.json()
                    if (this.errorCallback) {
                        await this.errorCallback(this.data, errorResponse)
                    }

                    this.error = error.message

                    if (this.alert) {
                        error.errors.forEach((error) => {
                            Notify(error.message, 'error')
                        })
                    }

                    return errorResponse
                })

                if (response.errors) {
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
                    Notify(this.notify.message, this.notify.type ?? 'success')
                }

                if (this.redirect) {
                    if (this.notify.message) {
                        document.addEventListener(
                            'turbo:load',
                            () => {
                                Notify(this.notify.message, this.notify.type ?? 'success')
                            },
                            { once: true },
                        )
                    }
                    Turbo.visit(window.url(this.redirect))
                }
            } catch (error) {
                console.error(error)
                this.error = error.message
                Notify(window.config.translations.errors.wrong, 'warning')
                throw error
            } finally {
                this.mutating = false
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
    computed: {
        running() {
            return this.mutating
        }
    }
}
</script>
