<script>
export default {
    methods: {
        // Get a nested difference between two objects
        // The values of source will be returned
        objectDiff: function (target, source) {
            return Object.fromEntries(
                Object.entries(source)
                    .map(([key, val]) => {
                        if ((!key) in target) {
                            return [key, val]
                        }

                        if (
                            target[key] === val ||
                            ((val === null || val === undefined) && (target[key] === null || target[key] === undefined))
                        ) {
                            return null
                        }

                        if (val instanceof Object) {
                            const diff = this.objectDiff(target[key], val)
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
        },

        // Merge two objects recursively
        deepMerge: function (target, source) {
            for (const key in source) {
                if (source[key] instanceof Object && key in target) {
                    target[key] = this.deepMerge(target[key], source[key])
                } else {
                    target[key] = source[key]
                }
            }

            return target
        },
    },
}
</script>
