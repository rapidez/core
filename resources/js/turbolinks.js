import * as Turbo from '@hotwired/turbo'

Turbo.config.drive.progressBarDelay = 5

document.addEventListener('turbo:before-visit', function (e) {
    if (typeof history.state.turbo === 'undefined') {
        // Trigger turbo to add the state.
        Turbo?.navigator?.history?.replace(window.location)
    }
})

document.addEventListener('vue:loaded', (e) => {
    // https://github.com/vuejs/core/issues/6154
    // This ensures the `v-bind:muted.attr` attribute is set before vue mounts
    // This way you will only need to add `muted` to the video element
    let videos = document.querySelectorAll('video[muted]');
    for(let i = 0; i < videos.length; i++) {
        console.log(videos[i]);
        videos[i].setAttribute('v-bind:muted.attr', true);
    }
})

document.addEventListener('turbo:before-render', (event) => {
    // https://github.com/hotwired/turbo-rails/issues/147
    // Before storing in cache we disable autoplay, but add autoplay it to the dataset.
    // This way it will not keep playing after navigating away
    let videos = document.querySelectorAll('video[autoplay]');
    for(let i = 0; i < videos.length; i++) {
        videos[i].autoplay = false;
        videos[i].dataset.autoplay = true;
    }

    // https://github.com/vuejs/core/issues/6154
    // Before storing in cache re-add the attribute since Vue removes it
    // upon mounting
    videos = document.querySelectorAll('video[muted]');
    for(let i = 0; i < videos.length; i++) {
        videos[i].setAttribute('v-bind:muted.attr', true);
    }
});

document.addEventListener('turbo:load', (event) => {
    // https://github.com/hotwired/turbo-rails/issues/147
    // On load from cache re-enable autoplay and remove the dataset attribute
    let videos = document.querySelectorAll('video[data-autoplay]');
    for(let i = 0; i < videos.length; i++) {
        videos[i].autoplay = true;
        delete videos[i].dataset.autoplay
    }
});
