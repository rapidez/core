export let pendingNotifications = []

export function pushNotification(notification) {
    let hasPreviousNotifications = pendingNotifications.length > 0
    pendingNotifications.push(notification)

    // Immediately trigger notifications if possible
    if (window.app && window.app.$emit) {
        triggerNotifications()
    }
}

export function triggerNotifications() {
    // Use a temp variable and insta-clear to avoid race conditions as much as possible
    let notifications = pendingNotifications
    pendingNotifications = []

    notifications.forEach(notification => {
        window.app.$emit('notification-message', notification.message ?? '', notification.type ?? 'info', notification.params ?? [], notification.link ?? null)
    })
}

window.document.addEventListener('vue:loaded', triggerNotifications)
