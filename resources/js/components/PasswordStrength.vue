<script>

export default {
    props: {
        password: {
            type: String,
            default: ''
        },
        minLength: {
            type: Number,
            default: null
        },
        minClasses: {
            type: Number,
            default: null
        },
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    computed: {
        errors: function () {
            let errors = []
            if (!/[a-z]/.test(this.password)) {
                errors.push(window.config.translations.password.lowercase)
            }
            if (!/[A-Z]/.test(this.password)) {
                errors.push(window.config.translations.password.uppercase)
            }
            if (!/\d/.test(this.password)) {
                errors.push(window.config.translations.password.number)
            }
            if (!/[^a-zA-Z0-9]/.test(this.password)) {
                errors.push(window.config.translations.password.special)
            }
            let satisfiedClasses = 4 - errors.length
            if (satisfiedClasses >= this.minClasses) {
                errors = []
            }
            if (this.password.length < this.minLength) {
                errors.push(window.config.translations.password.characters.replace(':minLength', this.minLength))
            }
            return errors
        },
        strengths: function () {
            let strengths = []
            if (this.minLength <= this.password.length) {
                strengths.push(window.config.translations.password.characters.replace(':minLength', this.minLength))
            }
            if (/[a-z]/.test(this.password)) {
                strengths.push(window.config.translations.password.lowercase)
            }
            if (/[A-Z]/.test(this.password)) {
                strengths.push(window.config.translations.password.uppercase)
            }
            if (/\d/.test(this.password)) {
                strengths.push(window.config.translations.password.number)
            }
            if (/[^a-zA-Z0-9]/.test(this.password)) {
                strengths.push(window.config.translations.password.special)
            }
            return strengths
        },
    },
}
</script>
