// TODO: Double check if we need this somewhere,
// for example with the text/bg utilities.
function color(variable, fallback) {
    return 'color-mix(in srgb, var(' + variable + ', ' + fallback + ') calc(100% * <alpha-value>), transparent)'
}

// TODO: Remove this files when everything is migrated.
