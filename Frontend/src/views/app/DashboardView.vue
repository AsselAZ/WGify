<template>
  <div class="min-h-screen">
    <AppNavbar title="Dashboard" :subtitle="welcomeSubtitle" />

    <div class="p-4 md:p-6 space-y-6">
      <!-- Stats -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <DashboardCard
          title="Gesamtausgaben"
          :value="`${total().toFixed(2)} EUR`"
          subtitle="Diesen Monat"
          :icon="Receipt"
          :trend="{ value: 12, positive: false }"
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
          :value="`${(total() / membersStore.members.length).toFixed(2)} EUR`"
          subtitle="Durchschnitt"
          :icon="TrendingUp"
          :trend="{ value: 5, positive: true }"
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
              v-for="(expense, index) in expensesStore.expenses.slice(0, 5)"
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
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
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

const { total } = expensesStore

onMounted(() => {
  authStore.loadUser()
  expensesStore.loadExpenses()
  tasksStore.loadTasks()
})

const welcomeSubtitle = computed(() => {
  const name = authStore.currentUser?.name ?? 'Gast'
  return `Willkommen zurück, ${name}!`
})

const openTasks = computed(() => tasksStore.tasks.filter(t => t.status === 'offen'))
const openTasksCount = computed(() => openTasks.value.length)
const completedTasksCount = computed(() => tasksStore.tasks.filter(t => t.status === 'erledigt').length)

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('de-DE')
}
</script>