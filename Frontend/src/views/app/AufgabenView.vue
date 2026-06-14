<template>
  <div class="min-h-screen">
    <AppNavbar title="Aufgaben" subtitle="Verwalte alle WG-Aufgaben" />

    <div class="p-4 md:p-6 space-y-6">
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <DashboardCard title="Alle Aufgaben" :value="store.tasks.length" subtitle="Insgesamt" :icon="ListTodo" />
        <DashboardCard title="Offen" :value="openCount" subtitle="Noch zu erledigen" :icon="Clock" />
        <DashboardCard title="Erledigt" :value="doneCount" subtitle="Abgeschlossen" :icon="CheckCircle2" />
        <DashboardCard title="Fortschritt" :value="`${completionRate}%`" subtitle="Erledigungsrate" :icon="AlertCircle" />
      </div>

      <div class="rounded-xl border border-border bg-card p-6">
        <TaskTable
          :tasks="store.tasks"
          @add-task="store.addTask"
          @toggle-status="store.toggleStatus"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { ListTodo, Clock, CheckCircle2, AlertCircle } from 'lucide-vue-next'
import AppNavbar from '@/components/AppNavbar.vue'
import DashboardCard from '@/components/DashboardCard.vue'
import TaskTable from '@/components/TaskTable.vue'
import { useTasksStore } from '@/stores/tasks'

const store = useTasksStore()

// Wenn die Aufgaben-Seite geöffnet wird,
// lädt das Frontend die Tasks aus Laravel/MySQL.
onMounted(() => {
  store.loadTasks()
})

const openCount = computed(() => store.tasks.filter(t => t.status === 'offen').length)
const doneCount = computed(() => store.tasks.filter(t => t.status === 'erledigt').length)

const completionRate = computed(() =>
  store.tasks.length > 0
    ? Math.round((doneCount.value / store.tasks.length) * 100)
    : 0
)
</script>