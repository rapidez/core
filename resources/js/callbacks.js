Vue.prototype.scrollToId = (id) => {
    let el = window.document.getElementById(id)
    window.scrollTo({
        top: el.offsetTop,
        behavior: 'smooth',
    })
}
