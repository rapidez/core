// Monkey patch Turbolinks to render 404 normally
// See: https://github.com/turbolinks/turbolinks/issues/179
window.Turbolinks.HttpRequest.prototype.requestLoaded = function() {
    return this.endRequest(function() {
        var code = this.xhr.status;
        if (code >= 200 && code < 300 || code === 404) {
            var redirectedToLocation = this.xhr.getResponseHeader("Turbolinks-Location");
            this.delegate.requestCompletedWithResponse(this.xhr.responseText, redirectedToLocation);
        } else {
            this.failed = true;
            this.delegate.requestFailedWithStatusCode(code, this.xhr.responseText);
        }
    }.bind(this));
};

// Fix turbolinks to remember page state after pushstate has been used.
// See: https://github.com/turbolinks/turbolinks/issues/219
document.addEventListener('turbolinks:before-visit', function(e){
    if (typeof history.state.turbolinks === 'undefined'){
        history.replaceState({ turbolinks: {} }, '');
    }
});
