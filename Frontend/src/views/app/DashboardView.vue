<template>
  <div class="min-h-screen">
    <AppNavbar
      title="Dashboard"
      :subtitle="welcomeSubtitle"
      v-model:search="searchQuery"
    />

    <div class="p-4 md:p-6 space-y-6">
      <!-- Stats -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
      <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-border bg-card p-6">
          <h3 class="mb-4 text-lg font-semibold">Letzte Ausgaben</h3>

          <div class="space-y-3">
            <div
              v-for="(expense, index) in filteredExpenses.slice(0, 5)"
              :key="expense.id"
              :class="[
                'flex items-center justify-between rounded-lg px-4 py-3',
                index % 3 === 0
                  ? 'bg-secondary/50'
                  : index % 3 === 1
                    ? 'bg-accent/30'
                    : 'bg-purple/10'
              ]"
            >
              <div>
                <p class="font-medium">{{ expense.title }}</p>
                <p class="text-sm text-muted-foreground">
                  {{ expense.paidBy }} - {{ formatDate(expense.date) }}
                </p>
              </div>

              <p class="font-semibold">{{ expense.amount.toFixed(2) }} EUR</p>
            </div>

            <p
              v-if="filteredExpenses.length === 0"
              class="text-sm text-muted-foreground"
            >
              Keine passenden Ausgaben gefunden.
            </p>
          </div>
        </div>

        <div class="rounded-xl border border-border bg-card p-6">
          <h3 class="mb-4 text-lg font-semibold">Offene Aufgaben</h3>

          <div class="space-y-3">
            <div
              v-for="(task, index) in openTasks.slice(0, 5)"
              :key="task.id"
              :class="[
                'flex items-center justify-between rounded-lg px-4 py-3',
                index % 2 === 0 ? 'bg-accent/30' : 'bg-secondary/50'
              ]"
            >
              <div>
                <p class="font-medium">{{ task.title }}</p>
                <p class="text-sm text-muted-foreground">
                  {{ task.assignedTo }} - Fällig: {{ formatDate(task.dueDate) }}
                </p>
              </div>

              <span class="inline-flex items-center rounded-full bg-warning/20 px-2.5 py-0.5 text-xs font-medium text-warning-foreground">
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

  if (!query) {
    return expensesStore.expenses
  }

  return expensesStore.expenses.filter(expense =>
    expense.title.toLowerCase().includes(query) ||
    expense.category.toLowerCase().includes(query) ||
    expense.paidBy.toLowerCase().includes(query) ||
    expense.date.toLowerCase().includes(query) ||
    String(expense.amount).includes(query)
  )
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

const openTasks = computed(() =>
  filteredTasks.value.filter(task => task.status === 'offen')
)

const openTasksCount = computed(() => openTasks.value.length)

const completedTasksCount = computed(() =>
  filteredTasks.value.filter(task => task.status === 'erledigt').length
)

function formatDate(date) {
  return new Date(date).toLocaleDateString('de-DE')
}
</script>