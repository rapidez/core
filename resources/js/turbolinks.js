import * as Turbo from '@hotwired/turbo'

import TurbolinksAdapter from 'vue-turbolinks'
Vue.use(TurbolinksAdapter)

Turbo.config.drive.progressBarDelay = 5

document.addEventListener('turbo:before-visit', function (e) {
    if (typeof history.state.turbo === 'undefined') {
        // Trigger turbo to add the state.
        Turbo?.navigator?.history?.replace(window.location)
    }
})
