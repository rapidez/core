import * as Turbo from '@hotwired/turbo'

import TurbolinksAdapter from 'vue-turbolinks'
Vue.use(TurbolinksAdapter)

Turbo.config.drive.progressBarDelay = 5
