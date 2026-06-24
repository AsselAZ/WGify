import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '@/lib/api'

export const useExpensesStore = defineStore('expenses', () => {
  const expenses = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  async function loadExpenses() {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.get('/expenses')

      expenses.value = response.data.map(expense => ({
        ...expense,
        id: String(expense.id),
      }))
    } catch (e) {
      error.value = 'Ausgaben konnten nicht geladen werden'
      console.error(e)
    } finally {
      isLoading.value = false
    }
  }

  async function addExpense(expense) {
    error.value = null

    try {
      const response = await api.post('/expenses', expense)

      expenses.value.unshift({
        ...response.data,
        id: String(response.data.id),
      })
    } catch (e) {
      error.value = 'Ausgabe konnte nicht gespeichert werden'
      console.error(e)
    }
  }

  const total = () => expenses.value.reduce((sum, expense) => sum + Number(expense.amount), 0)

  return {
    expenses,
    isLoading,
    error,
    loadExpenses,
    addExpense,
    total,
  }
})