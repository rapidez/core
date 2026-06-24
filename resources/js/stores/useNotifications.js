import { ref, computed } from 'vue'

export let pendingNotifications = ref([])

export const notificationCount = computed(() => {
    return pendingNotifications.value.length
})

export function pushNotification(notification) {
    let hasPreviousNotifications = pendingNotifications.length > 0
    pendingNotifications.value.push(notification)
}

export function clearNotifications() {
    pendingNotifications.value = []
}
