import * as Turbo from "@hotwired/turbo"

import TurbolinksAdapter from 'vue-turbolinks'
Vue.use(TurbolinksAdapter)

window.addEventListener('popstate', (event) => {
    if (typeof event.state.turbo === 'undefined') {
        Turbo.visit(new URL(event.state.path))
    }
})
