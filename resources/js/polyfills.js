import './polyfills/emit'

if (!window.requestIdleCallback || !window.cancelIdleCallback) {
    import('./polyfills/requestIdleCallback')
}
