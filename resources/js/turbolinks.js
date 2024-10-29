import * as Turbo from '@hotwired/turbo'
import { PageRenderer } from '@hotwired/turbo'

import TurbolinksAdapter from 'vue-turbolinks'
Vue.use(TurbolinksAdapter)

Turbo.setProgressBarDelay(5)

// Patch the internal Turbo renderer to use #turbo-wrapper as the root node instead of the body
// This allows things placed in the body by scripts like GTM to persist
Object.assign(PageRenderer.prototype, {
    assignNewBody() {
        const container = document.querySelector('#turbo-wrapper')
        const newContainer = this.newElement.querySelector('#turbo-wrapper')

        if (container && newContainer) {
            container.replaceWith(newContainer)
        } else {
            document.body.replaceWith(this.newElement)
        }
    },
})
