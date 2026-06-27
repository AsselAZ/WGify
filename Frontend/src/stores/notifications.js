import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

export const useNotificationsStore = defineStore('notifications', () => {
  const notifications = ref([])

  const unreadCount = computed(() => {
    return notifications.value.filter((notification) => !notification.read).length
  })

  function normalizeUserKey(userKey) {
    return String(userKey || 'guest').trim().toLowerCase()
  }

  function getCurrentUserKey() {
    const authStore = useAuthStore()
    return authStore.currentUser?.email || authStore.currentUser?.id || 'guest'
  }

  function getStorageKey(userKey = null) {
    const finalUserKey = userKey || getCurrentUserKey()
    return `notifications-${normalizeUserKey(finalUserKey)}`
  }

  function readNotifications(userKey = null) {
    const savedNotifications = localStorage.getItem(getStorageKey(userKey))

    if (!savedNotifications) {
      return []
    }

    return JSON.parse(savedNotifications)
  }

  function writeNotifications(userKey, items) {
    localStorage.setItem(getStorageKey(userKey), JSON.stringify(items))
  }

  function loadNotifications() {
    notifications.value = readNotifications()
  }

  function saveNotifications() {
    writeNotifications(getCurrentUserKey(), notifications.value)
  }

  function addNotification(type, title, message) {
    addNotificationForUser(getCurrentUserKey(), type, title, message)
  }

  function addNotificationForUser(userKey, type, title, message) {
    const targetStorageKey = getStorageKey(userKey)
    const currentStorageKey = getStorageKey()

    const items = readNotifications(userKey)

    const exists = items.some((notification) => {
      return (
        notification.type === type &&
        notification.title === title &&
        notification.message === message
      )
    })

    if (exists) {
      return
    }

    const newItems = [
      {
        id: `${Date.now()}-${Math.random()}`,
        type,
        title,
        message,
        read: false,
        createdAt: new Date().toISOString(),
      },
      ...items,
    ]

    writeNotifications(userKey, newItems)

    if (targetStorageKey === currentStorageKey) {
      notifications.value = newItems
    }
  }

  function markAllAsRead() {
    notifications.value = notifications.value.map((notification) => ({
      ...notification,
      read: true,
    }))

    saveNotifications()
  }

  function clearNotifications() {
    notifications.value = []
    saveNotifications()
  }

  return {
    notifications,
    unreadCount,
    loadNotifications,
    addNotification,
    addNotificationForUser,
    markAllAsRead,
    clearNotifications,
  }
})