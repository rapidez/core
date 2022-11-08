import Vue from 'vue'
window.Vue = Vue
import AsyncComputed from 'node_modules/vue-async-computed'
Vue.use(AsyncComputed)

import { directive as onClickaway } from 'node_modules/vue-clickaway'
Vue.directive('on-click-away', onClickaway)
