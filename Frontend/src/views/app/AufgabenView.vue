<template>
  <div class="min-h-screen">
    <AppNavbar
      title="Aufgaben"
      subtitle="Verwalte alle WG-Aufgaben"
      v-model:search="searchQuery"
    />

    <div class="p-4 md:p-6 space-y-6">
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <DashboardCard
          title="Alle Aufgaben"
          :value="filteredTasks.length"
          subtitle="Gefiltert"
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
          subtitle="Erledigungsrate"
          :icon="AlertCircle"
        />
      </div>

      <div class="rounded-xl border border-border bg-card p-6">
        <TaskTable
          :tasks="filteredTasks"
          @add-task="store.addTask"
          @toggle-status="store.toggleStatus"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { ListTodo, Clock, CheckCircle2, AlertCircle } from 'lucide-vue-next'
import AppNavbar from '@/components/AppNavbar.vue'
import DashboardCard from '@/components/DashboardCard.vue'
import TaskTable from '@/components/TaskTable.vue'
import { useTasksStore } from '@/stores/tasks'

const store = useTasksStore()

const searchQuery = ref('')

// Wenn die Aufgaben-Seite geöffnet wird,
// lädt das Frontend die Tasks aus Laravel/MySQL.
onMounted(() => {
  store.loadTasks()
})

const filteredTasks = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()

  if (!query) {
    return store.tasks
  }

  return store.tasks.filter(task =>
    task.title.toLowerCase().includes(query) ||
    task.assignedTo.toLowerCase().includes(query) ||
    task.dueDate.toLowerCase().includes(query) ||
    task.status.toLowerCase().includes(query)
  )
})

const openCount = computed(() =>
  filteredTasks.value.filter(task => task.status === 'offen').length
)

const doneCount = computed(() =>
  filteredTasks.value.filter(task => task.status === 'erledigt').length
)

const completionRate = computed(() =>
  filteredTasks.value.length > 0
    ? Math.round((doneCount.value / filteredTasks.value.length) * 100)
    : 0
)
</script>