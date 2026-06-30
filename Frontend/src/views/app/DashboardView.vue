<template>
  <div class="min-h-screen">
    <AppNavbar
      title="Dashboard"
      :subtitle="welcomeSubtitle"
      v-model:search="searchQuery"
    />

    <div class="space-y-6 p-4 sm:p-6">
      <!-- Stats -->
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <DashboardCard
          title="Gesamtausgaben"
          :value="`${total().toFixed(2)} EUR`"
          subtitle="Diesen Monat"
          :icon="Receipt"
          :trend="null"
          iconBgClass="bg-purple/15"
          iconColorClass="text-purple"
        />

        <DashboardCard
          title="Offene Aufgaben"
          :value="openTasksCount"
          :subtitle="`${completedTasksCount} erledigt`"
          :icon="ListTodo"
          iconBgClass="bg-accent/40"
          iconColorClass="text-primary"
        />

        <DashboardCard
          title="Mitglieder"
          :value="membersStore.members.length"
          subtitle="Aktive WG-Bewohner"
          :icon="Users"
          iconBgClass="bg-secondary"
          iconColorClass="text-primary"
        />

        <DashboardCard
          title="Pro Person"
          :value="`${avgPerPerson.toFixed(2)} EUR`"
          subtitle="Durchschnitt"
          :icon="TrendingUp"
          :trend="null"
          iconBgClass="bg-primary/10"
          iconColorClass="text-primary"
        />
      </div>

      <!-- Recent Activity -->
      <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-xl border border-border bg-card p-4 sm:p-6">
          <h3 class="mb-4 text-lg font-semibold">
            Letzte Ausgaben
          </h3>

          <div class="space-y-3">
            <div
              v-for="(expense, index) in filteredExpenses.slice(0, 5)"
              :key="expense.id"
              :class="[
                'flex flex-col gap-2 rounded-lg px-4 py-3 sm:flex-row sm:items-center sm:justify-between',
                index % 3 === 0
                  ? 'bg-secondary/50'
                  : index % 3 === 1
                    ? 'bg-accent/30'
                    : 'bg-purple/10',
              ]"
            >
              <div class="min-w-0">
                <p class="truncate font-medium">
                  {{ expense.title }}
                </p>

                <p class="text-sm text-muted-foreground">
                  {{ expense.paidBy }} - {{ formatDate(expense.date) }}
                </p>
              </div>

              <p class="shrink-0 whitespace-nowrap font-semibold sm:text-right">
                {{ expense.amount.toFixed(2) }} EUR
              </p>
            </div>

            <p
              v-if="filteredExpenses.length === 0"
              class="text-sm text-muted-foreground"
            >
              Keine passenden Ausgaben gefunden.
            </p>
          </div>
        </div>

        <div class="rounded-xl border border-border bg-card p-4 sm:p-6">
          <h3 class="mb-4 text-lg font-semibold">
            Offene Aufgaben
          </h3>

          <div class="space-y-3">
            <div
              v-for="(task, index) in openTasks.slice(0, 5)"
              :key="task.id"
              :class="[
                'flex flex-col gap-2 rounded-lg px-4 py-3 sm:flex-row sm:items-center sm:justify-between',
                index % 2 === 0 ? 'bg-accent/30' : 'bg-secondary/50',
              ]"
            >
              <div class="min-w-0">
                <p class="truncate font-medium">
                  {{ task.title }}
                </p>

                <p class="text-sm text-muted-foreground">
                  {{ task.assignedTo }} - Fällig: {{ formatDate(task.dueDate) }}
                </p>
              </div>

              <span class="inline-flex w-fit shrink-0 items-center rounded-full bg-warning/20 px-2.5 py-0.5 text-xs font-medium text-warning-foreground">
                Offen
              </span>
            </div>

            <p
              v-if="openTasks.length === 0"
              class="text-sm text-muted-foreground"
            >
              Keine passenden offenen Aufgaben gefunden.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { Receipt, ListTodo, Users, TrendingUp } from 'lucide-vue-next'
import AppNavbar from '@/components/AppNavbar.vue'
import DashboardCard from '@/components/DashboardCard.vue'
import { useExpensesStore } from '@/stores/expenses'
import { useTasksStore } from '@/stores/tasks'
import { useMembersStore } from '@/stores/members'
import { useAuthStore } from '@/stores/auth'

const expensesStore = useExpensesStore()
const tasksStore = useTasksStore()
const membersStore = useMembersStore()
const authStore = useAuthStore()

const searchQuery = ref('')

const { total } = expensesStore

onMounted(() => {
  authStore.loadUser()
  expensesStore.loadExpenses()
  tasksStore.loadTasks()
  membersStore.loadMembers()
})

const welcomeSubtitle = computed(() => {
  const name = authStore.currentUser?.name ?? 'Gast'
  return `Willkommen zurück, ${name}!`
})

const avgPerPerson = computed(() =>
  membersStore.members.length > 0
    ? total() / membersStore.members.length
    : 0
)

const filteredExpenses = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  let expenses = expensesStore.expenses

  if (query) {
    expenses = expenses.filter(expense =>
      expense.title.toLowerCase().includes(query) ||
      expense.category.toLowerCase().includes(query) ||
      expense.paidBy.toLowerCase().includes(query) ||
      expense.date.toLowerCase().includes(query) ||
      String(expense.amount).includes(query)
    )
  }

  // Sortierung: neueste zuerst
  return [...expenses].sort((a, b) => {
    return new Date(b.date) - new Date(a.date)
  })
})

const filteredTasks = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()

  if (!query) {
    return tasksStore.tasks
  }

  return tasksStore.tasks.filter(task =>
    task.title.toLowerCase().includes(query) ||
    task.assignedTo.toLowerCase().includes(query) ||
    task.dueDate.toLowerCase().includes(query) ||
    task.status.toLowerCase().includes(query)
  )
})

const openTasks = computed(() => {
  // Die Tasks sortieren - Erst nach Status, dann nach Fälligkeitsdatum sortieren 
  return filteredTasks.value
    .filter(task => task.status === 'offen')
    .sort((a, b) => {
      return new Date(a.dueDate) - new Date(b.dueDate)
    })
})

const openTasksCount = computed(() => openTasks.value.length)

const completedTasksCount = computed(() =>
  filteredTasks.value.filter(task => task.status === 'erledigt').length
)

function formatDate(date) {
  return new Date(date).toLocaleDateString('de-DE')
}
</script>