<template>
  <div class="min-h-screen">
    <AppNavbar
      title="Ausgaben"
      subtitle="Verwalte alle WG-Ausgaben"
      v-model:search="searchQuery"
    />

    <div class="p-4 md:p-6 space-y-6">
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <DashboardCard
          title="Gesamtausgaben"
          :value="`${totalExpenses.toFixed(2)} €`"
          subtitle="Alle Ausgaben"
          :icon="Receipt"
        />

        <DashboardCard
          title="Pro Person"
          :value="`${avgPerPerson.toFixed(2)} €`"
          subtitle="Durchschnitt"
          :icon="TrendingUp"
        />

        <DashboardCard
          title="Kategorien"
          :value="categoryCount"
          subtitle="Verschiedene"
          :icon="PieChart"
        />

        <DashboardCard
          title="Einträge"
          :value="filteredExpenses.length"
          subtitle="Gefilterte Einträge"
          :icon="Calendar"
        />
      </div>

      <div class="rounded-xl border border-border bg-card p-6">
        <ExpenseTable
          :expenses="filteredExpenses"
          :members="membersStore.members"
          @add-expense="store.addExpense"
          @update-expense="store.updateExpense"
          @delete-expense="store.deleteExpense"
        />
      </div>
    </div>
  </div>
</template>

<script setup>

import { computed, onMounted, ref } from 'vue'
import { Receipt, TrendingUp, PieChart, Calendar } from 'lucide-vue-next'
import AppNavbar from '@/components/AppNavbar.vue'
import DashboardCard from '@/components/DashboardCard.vue'
import ExpenseTable from '@/components/ExpenseTable.vue'
import { useExpensesStore } from '@/stores/expenses'
import { useMembersStore } from '@/stores/members'

const store = useExpensesStore()
const membersStore = useMembersStore()

const searchQuery = ref('')

onMounted(() => {
  store.loadExpenses()
  membersStore.loadMembers()
})

const filteredExpenses = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  let expenses = store.expenses

  if (query) {
    expenses = expenses.filter(expense =>
      expense.title.toLowerCase().includes(query) ||
      expense.category.toLowerCase().includes(query) ||
      expense.paidBy.toLowerCase().includes(query) ||
      expense.date.toLowerCase().includes(query) ||
      String(expense.amount).includes(query)
    )
  }

  // Sortierung: neueste Ausgaben zuerst
  return [...expenses].sort((a, b) => {
    return new Date(b.date) - new Date(a.date)
  })
})

const totalExpenses = computed(() =>
  filteredExpenses.value.reduce((sum, expense) => sum + expense.amount, 0)
)

const avgPerPerson = computed(() =>
  membersStore.members.length > 0
    ? totalExpenses.value / membersStore.members.length
    : 0
)

const categoryCount = computed(() =>
  new Set(filteredExpenses.value.map(expense => expense.category)).size
)
</script>