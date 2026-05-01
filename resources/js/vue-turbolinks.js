function handleVueDestruction(instance) {
    const event = instance.$options.destroyEvent || defaultEvent()

    document.addEventListener(event, function teardown() {
        const cachedHTML = instance._.type.template
        const el = instance.$el
        const parent = el ? el.parentNode : null

        instance.$.appContext.app.unmount()

        // Vue 3's unmount() removes DOM nodes, so restore cached HTML into the parent manually
        if (parent && cachedHTML && parent.innerHTML === '') {
            parent.innerHTML = cachedHTML
        }

        document.removeEventListener(event, teardown)
    })
}

const Mixin = {
    beforeMount() {
        if (this !== this.$root) {
            // We only wish to unmount the root component.
            return
        }

        handleVueDestruction(this)
    },
}

function plugin(app, options) {
    // Install a global mixin
    app.mixin(Mixin)
}

function defaultEvent() {
    if (typeof Turbo !== 'undefined') {
        return 'turbo:visit'
    }

    return 'turbolinks:visit'
}

export { Mixin as turbolinksAdapterMixin }
export default plugin
