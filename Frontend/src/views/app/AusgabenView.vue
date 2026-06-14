<template>
  <div class="min-h-screen">
    <AppNavbar title="Ausgaben" subtitle="Verwalte alle WG-Ausgaben" />
    <div class="p-4 md:p-6 space-y-6">
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <DashboardCard title="Gesamtausgaben" :value="`${totalExpenses.toFixed(2)} €`" subtitle="Alle Ausgaben" :icon="Receipt" />
        <DashboardCard title="Pro Person" :value="`${avgPerPerson.toFixed(2)} €`" subtitle="Durchschnitt" :icon="TrendingUp" />
        <DashboardCard title="Kategorien" :value="categoryCount" subtitle="Verschiedene" :icon="PieChart" />
        <DashboardCard title="Einträge" :value="store.expenses.length" subtitle="Diesen Monat" :icon="Calendar" />
      </div>
      <div class="rounded-xl border border-border bg-card p-6">
        <ExpenseTable :expenses="store.expenses" @add-expense="store.addExpense" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { Receipt, TrendingUp, PieChart, Calendar } from 'lucide-vue-next'
import AppNavbar from '@/components/AppNavbar.vue'
import DashboardCard from '@/components/DashboardCard.vue'
import ExpenseTable from '@/components/ExpenseTable.vue'
import { useExpensesStore } from '@/stores/expenses'
import { useMembersStore } from '@/stores/members'

const store = useExpensesStore()
const membersStore = useMembersStore()

onMounted(() => {
  store.loadExpenses()
})

const totalExpenses = computed(() => store.expenses.reduce((s, e) => s + e.amount, 0))
const avgPerPerson = computed(() => totalExpenses.value / membersStore.members.length)
const categoryCount = computed(() => new Set(store.expenses.map(e => e.category)).size)

</script>
