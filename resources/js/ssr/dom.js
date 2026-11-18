// POC: a fake browser environment so the existing Rapidez modules, which
// expect `window` and `document`, can be imported and rendered in Node.
// Must be imported before anything else in the SSR entry.
import { Window } from 'happy-dom'

const browser = new Window({
    url: 'http://localhost/',
    settings: {
        disableJavaScriptEvaluation: true,
        disableJavaScriptFileLoading: true,
        disableCSSFileLoading: true,
        disableIframePageLoading: true,
        navigator: { userAgent: 'Rapidez SSR' },
    },
})

const forward = [
    'document',
    'location',
    'history',
    'navigator',
    'localStorage',
    'sessionStorage',
    'screen',
    'innerWidth',
    'innerHeight',
    'Node',
    'Element',
    'HTMLElement',
    'HTMLInputElement',
    'DocumentFragment',
    'Text',
    'Event',
    'CustomEvent',
    'EventTarget',
    'KeyboardEvent',
    'MouseEvent',
    'FocusEvent',
    'InputEvent',
    'MutationObserver',
    'IntersectionObserver',
    'ResizeObserver',
    'DOMParser',
    'Image',
    'getComputedStyle',
    'matchMedia',
    'requestAnimationFrame',
    'cancelAnimationFrame',
    'requestIdleCallback',
    'scrollTo',
    'addEventListener',
    'removeEventListener',
    'dispatchEvent',
]

for (const key of forward) {
    Object.defineProperty(globalThis, key, {
        configurable: true,
        get: () => (typeof browser[key] === 'function' && !/^[A-Z]/.test(key) ? browser[key].bind(browser) : browser[key]),
        set: (value) => Object.defineProperty(globalThis, key, { configurable: true, writable: true, value }),
    })
}

// In the browser `window` is the global object, so `window.config = ...`
// makes `config` available as a bare global. Mimic that.
globalThis.window = globalThis
globalThis.self = globalThis
globalThis.happyDOM = browser.happyDOM

// In the browser config.js is loaded before the app, some modules read it on
// import (helpers.js creates a number formatter with the locale). Here it's
// set per request, so on import it's empty and those keep the default locale.
globalThis.config = {}

// Stubs for globals that are only used on interaction.
globalThis.Turbo = { visit: () => {} }

export function setUrl(url) {
    browser.happyDOM.setURL(url)
}
