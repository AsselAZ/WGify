<template>
  <div class="relative">
    <button
      type="button"
      class="relative flex h-10 w-10 items-center justify-center rounded-md transition-colors hover:bg-muted"
      aria-label="Benachrichtigungen öffnen"
      @click="isOpen = !isOpen"
    >
      <Bell class="h-5 w-5" />

      <span
        v-if="notificationsStore.unreadCount > 0"
        class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-xs font-semibold text-white"
      >
        {{ notificationsStore.unreadCount }}
      </span>
    </button>

    <div
      v-if="isOpen"
      class="fixed inset-x-3 top-16 z-50 max-h-[calc(100vh-5rem)] overflow-hidden rounded-xl border border-border bg-card shadow-xl sm:absolute sm:inset-x-auto sm:right-0 sm:top-11 sm:w-80"
    >
      <div class="flex items-start justify-between gap-3 border-b border-border p-4">
        <div class="min-w-0">
          <h3 class="font-semibold">
            Benachrichtigungen
          </h3>

          <p class="text-xs text-muted-foreground">
            {{ notificationsStore.unreadCount }} ungelesen
          </p>
        </div>

        <button
          type="button"
          class="shrink-0 text-xs text-primary hover:underline"
          @click="notificationsStore.markAllAsRead()"
        >
          Alle gelesen
        </button>
      </div>

      <div class="max-h-[calc(100vh-12rem)] overflow-y-auto sm:max-h-96">
        <div
          v-if="notificationsStore.notifications.length === 0"
          class="p-4 text-sm text-muted-foreground"
        >
          Keine Benachrichtigungen vorhanden.
        </div>

        <div
          v-for="notification in notificationsStore.notifications"
          :key="notification.id"
          class="border-b border-border p-4 last:border-b-0"
          :class="notification.read ? 'opacity-70' : 'bg-muted/30'"
        >
          <p class="text-sm font-medium">
            {{ notification.title }}
          </p>

          <p class="mt-1 text-sm text-muted-foreground">
            {{ notification.message }}
          </p>

          <p class="mt-2 text-xs text-muted-foreground">
            {{ formatDate(notification.createdAt) }}
          </p>
        </div>
      </div>

      <div
        v-if="notificationsStore.notifications.length > 0"
        class="border-t border-border p-3 text-right"
      >
        <button
          type="button"
          class="text-xs text-red-600 hover:underline"
          @click="notificationsStore.clearNotifications()"
        >
          Alle löschen
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { Bell } from 'lucide-vue-next'
import { useNotificationsStore } from '@/stores/notifications'
import { api } from '@/lib/api'

const isOpen = ref(false)
const notificationsStore = useNotificationsStore()

onMounted(() => {
  notificationsStore.loadNotifications()
  loadOverdueTaskNotifications()
})

async function loadOverdueTaskNotifications() {
  try {
    const response = await api.get('/tasks/overdue')

    response.data.forEach((task) => {
      notificationsStore.addNotification(
        'task-overdue',
        'Überfällige Aufgabe',
        `Die Aufgabe "${task.title}" war am ${formatDateOnly(task.dueDate)} fällig.`
      )
    })
  } catch (error) {
    console.error(error)
  }
}

function formatDate(date) {
  return new Date(date).toLocaleString('de-DE', {
    dateStyle: 'short',
    timeStyle: 'short',
  })
}

function formatDateOnly(date) {
  return new Date(date).toLocaleDateString('de-DE')
}
</script>