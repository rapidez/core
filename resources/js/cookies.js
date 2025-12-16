import VueCookies from 'vue-cookies'

document.addEventListener('vue:loaded', function (event) {
    event.detail.vue.use(VueCookies)
})
