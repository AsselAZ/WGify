<template>
  <div class="min-h-screen">
    <AppNavbar
      title="Aufgaben"
      subtitle="Verwalte alle WG-Aufgaben"
      :search="searchQuery"
      @update:search="searchQuery = $event"
    />

    <div class="p-4 md:p-6 space-y-6">
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <DashboardCard
          title="Aufgaben"
          :value="filteredTasks.length"
          subtitle="Gesamt"
          :icon="ListTodo"
        />

        <DashboardCard
          title="Offen"
          :value="openCount"
          subtitle="Noch zu erledigen"
          :icon="Clock"
        />

        <DashboardCard
          title="Erledigt"
          :value="doneCount"
          subtitle="Abgeschlossen"
          :icon="CheckCircle2"
        />

        <DashboardCard
          title="Fortschritt"
          :value="`${completionRate}%`"
          subtitle="Erledigungsquote"
          :icon="AlertCircle"
        />
      </div>

      <div class="rounded-xl border border-border bg-card p-6">
        <p v-if="store.isLoading" class="text-sm text-muted-foreground">
          Aufgaben werden geladen...
        </p>

        <TaskTable
          :tasks="filteredTasks"
          :members="membersStore.members"
          @create-task="createTask"
          @update-task="updateTask"
          @delete-task="deleteTask"
          @toggle-task-status="toggleTaskStatus"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useToastStore } from '@/stores/toast'
import AppNavbar from '@/components/AppNavbar.vue'
import DashboardCard from '@/components/DashboardCard.vue'
import TaskTable from '@/components/TaskTable.vue'
import { useTasksStore } from '@/stores/tasks'
import { useMembersStore } from '@/stores/members'

import {
  ListTodo,
  Clock,
  CheckCircle2,
  AlertCircle,
} from 'lucide-vue-next'


const store = useTasksStore()
const membersStore = useMembersStore()
const toast = useToastStore()

const searchQuery = ref('')

onMounted(() => {
  store.loadTasks()
  membersStore.loadMembers()
})

const filteredTasks = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()

  let tasks = store.tasks

  if (query) {
    tasks = tasks.filter(task =>
      task.title.toLowerCase().includes(query) ||
      task.assignedTo.toLowerCase().includes(query) ||
      task.dueDate.toLowerCase().includes(query) ||
      task.status.toLowerCase().includes(query)
    )
  }

  // Die Tasks sortieren - Erst nach Status, dann nach Fälligkeitsdatum sortieren 
  return [...tasks].sort((a, b) => {
    // Offene Aufgaben vor erledigten
    if (a.status !== b.status) {
      return a.status === 'offen' ? -1 : 1
    }
    // Innerhalb des Status nach Deadline
    return new Date(a.dueDate) - new Date(b.dueDate)
  })
})

const openCount = computed(() => {
  return filteredTasks.value.filter(task => task.status === 'offen').length
})

const doneCount = computed(() => {
  return filteredTasks.value.filter(task => task.status === 'erledigt').length
})

const completionRate = computed(() => {
  if (filteredTasks.value.length === 0) {
    return 0
  }

  return Math.round((doneCount.value / filteredTasks.value.length) * 100)
})

//Für Toasts!! ---------------------------

async function createTask(task) {
  try {
    await store.addTask(task) 
    toast.success(
      'Aufgabe erstellt',
      `Die Aufgabe "${task.title}" wurde erfolgreich gespeichert.`
    )
  } catch (error) {
    toast.error(
      'Speichern fehlgeschlagen',
      error.response?.data?.message ||
        `Die Aufgabe "${task.title}" konnte nicht gespeichert werden.`
    )
  }
}

async function updateTask(id, task) {
  try {
    await store.updateTask(id, task)

    toast.success(
      'Aufgabe gespeichert',
      `Die Aufgabe "${task.title}" wurde erfolgreich gespeichert.`
    )
  } catch (error) {
    toast.error(
      'Speichern fehlgeschlagen',
      error.response?.data?.message ||
        `Die Aufgabe "${task.title}" konnte nicht gespeichert werden.`
    )
  }
}

async function deleteTask(id) {
  const task = store.tasks.find((item) => item.id === id)

  try {
    await store.deleteTask(id)

    toast.success(
      'Aufgabe gelöscht',
      `Die Aufgabe "${task?.title || 'Diese Aufgabe'}" wurde erfolgreich gelöscht.`
    )
  } catch (error) {
    toast.error(
      'Löschen fehlgeschlagen',
      error.response?.data?.message ||
        `Die Aufgabe "${task?.title || 'Diese Aufgabe'}" konnte nicht gelöscht werden.`
    )
  }
}

async function toggleTaskStatus(id, task) {
  try {
    await store.updateTask(id, task)

    if (task.status === 'erledigt') {
      toast.success(
        'Aufgabe erledigt',
        `Die Aufgabe "${task.title}" wurde als erledigt markiert.`
      )
    } else {
      toast.info(
        'Aufgabe wieder geöffnet',
        `Die Aufgabe "${task.title}" wurde wieder als offen markiert.`
      )
    }
  } catch (error) {
    toast.error(
      'Status konnte nicht geändert werden',
      error.response?.data?.message ||
        `Der Status der Aufgabe "${task.title}" konnte nicht geändert werden.`
    )
  }
}

</script>