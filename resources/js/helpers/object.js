/**
 * Get a nested difference between two objects
 * The values of source will be returned
 *
 * @param {Object} target
 * @param {Object} source
 *
 * @returns Object
 */
export function objectDiff(target, source) {
    return Object.fromEntries(
        Object.entries({
            // Ensure keys no longer in source will be set to undefined
            ...Object.fromEntries(Object.keys(target).map((key) => [key, undefined])),
            ...source,
        })
            .map(([key, val]) => {
                if (!target || !(key in target)) {
                    return [key, val]
                }

                if (
                    target[key] === val ||
                    ((val === null || val === undefined || isNaN(val)) &&
                        (target[key] === null || target[key] === undefined || isNaN(target[key])))
                ) {
                    return null
                }

                if (val instanceof Object) {
                    const diff = objectDiff(target[key], val)
                    if (Object.keys(diff).length === 0) {
                        return null
                    }
                    return [key, diff]
                }

                if (Array.isArray(val)) {
                    const diff = val.filter((x) => target[key].includes(x))
                    if (diff.length === 0) {
                        return null
                    }
                    // We cannot use diff here because the keys will not match up.
                    return [key, val]
                }

                return [key, val]
            })
            .filter(Boolean),
    )
}

/**
 * Merge two objects recursively
 * Adding or replacing values in target from source.
 *
 * @param {Object} target
 * @param {Object} source
 *
 * @returns Object
 */
export function deepMerge(target, source) {
    for (const key in source) {
        if (source[key] instanceof Object && key in target) {
            target[key] = deepMerge(target[key], source[key])
        } else {
            target[key] = source[key]
        }
    }

    return target
}
