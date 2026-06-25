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
        amount: Number(expense.amount),
      }))
    } catch (err) {
      error.value = 'Ausgaben konnten nicht geladen werden.'
      console.error(err)
    } finally {
      isLoading.value = false
    }
  }

  async function addExpense(expense) {
    const response = await api.post('/expenses', expense)

    expenses.value.unshift({
      ...response.data,
      id: String(response.data.id),
      amount: Number(response.data.amount),
    })

    return response.data
  }

  async function updateExpense(id, expense) {
    const response = await api.patch(`/expenses/${id}`, expense)

    const updatedExpense = {
      ...response.data,
      id: String(response.data.id),
      amount: Number(response.data.amount),
    }

    const index = expenses.value.findIndex(item => item.id === String(id))

    if (index !== -1) {
      expenses.value[index] = updatedExpense
    }

    return updatedExpense
  }

  async function deleteExpense(id) {
    await api.delete(`/expenses/${id}`)

    expenses.value = expenses.value.filter(expense => expense.id !== String(id))
  }

  function total() {
    return expenses.value.reduce((sum, expense) => {
      return sum + Number(expense.amount)
    }, 0)
  }

  return {
    expenses,
    isLoading,
    error,
    loadExpenses,
    addExpense,
    updateExpense,
    deleteExpense,
    total,
  }
})