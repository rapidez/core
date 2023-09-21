if (!window.requestIdleCallback || !window.cancelIdleCallback) {
    import('./polyfills/requestIdleCallback')
}
