window.requestIdleCallback =
    window.requestIdleCallback ||
    function (callback) {
        return setTimeout(() => {
            var start = Date.now()
            callback({
                didTimeout: false,
                timeRemaining: () => Math.max(0, 50 - (Date.now() - start)),
            })
        }, 1)
    }

window.cancelIdleCallback =
    window.cancelIdleCallback ||
    function (id) {
        clearTimeout(id)
    }
