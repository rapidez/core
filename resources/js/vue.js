import Vue from 'vue'
window.Vue = Vue

import AsyncComputed from 'vue-async-computed'
Vue.use(AsyncComputed)

import { directive as onClickaway } from 'vue-clickaway';
Vue.directive('on-click-away', onClickaway);
