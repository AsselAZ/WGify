<template>
  <div class="min-h-screen">
    <AppNavbar
      title="Ausgaben"
      subtitle="Verwalte alle WG-Ausgaben"
      v-model:search="searchQuery"
    />

    <div class="space-y-6 p-4 sm:p-6">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <DashboardCard
          title="Gesamtausgaben"
          :value="`${totalExpenses.toFixed(2)} EUR`"
          subtitle="Alle Ausgaben"
          :icon="Receipt"
        />

        <DashboardCard
          title="Pro Person"
          :value="`${avgPerPerson.toFixed(2)} EUR`"
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

      <div class="rounded-xl border border-border bg-card p-4 sm:p-6">
        <ExpenseTable
          :expenses="filteredExpenses"
          :members="membersStore.members"
          @add-expense="createExpense"
          @update-expense="updateExpense"
          @delete-expense="deleteExpense"
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
import { useToastStore } from '@/stores/toast'

const store = useExpensesStore()
const membersStore = useMembersStore()
const toast = useToastStore()
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

async function createExpense(expense) {
  try {
    await store.addExpense(expense)
    await store.loadExpenses()

    toast.success(
      'Ausgabe gespeichert',
      `Die Ausgabe "${expense.title}" wurde erfolgreich gespeichert.`
    )
  } catch (error) {
    toast.error(
      'Speichern fehlgeschlagen',
      error.response?.data?.message ||
        error.response?.data?.errors?.title?.[0] ||
        error.response?.data?.errors?.amount?.[0] ||
        error.response?.data?.errors?.category?.[0] ||
        error.response?.data?.errors?.paidBy?.[0] ||
        error.response?.data?.errors?.date?.[0] ||
        `Die Ausgabe "${expense.title}" konnte nicht gespeichert werden.`
    )
  }
}

async function updateExpense(id, expense) {
  try {
    await store.updateExpense(id, expense)
    await store.loadExpenses()

    toast.success(
      'Ausgabe gespeichert',
      `Die Ausgabe "${expense.title}" wurde erfolgreich gespeichert.`
    )
  } catch (error) {
    toast.error(
      'Speichern fehlgeschlagen',
      error.response?.data?.message ||
        `Die Ausgabe "${expense.title}" konnte nicht gespeichert werden.`
    )
  }
}

async function deleteExpense(id) {
  const expense = store.expenses.find((item) => item.id === id)

  try {
    await store.deleteExpense(id)
    await store.loadExpenses()

    toast.success(
      'Ausgabe gelöscht',
      `Die Ausgabe "${expense?.title || 'Diese Ausgabe'}" wurde erfolgreich gelöscht.`
    )
  } catch (error) {
    toast.error(
      'Löschen fehlgeschlagen',
      error.response?.data?.message ||
        `Die Ausgabe "${expense?.title || 'Diese Ausgabe'}" konnte nicht gelöscht werden.`
    )
  }
}

</script>