import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '@/lib/api'

export const useTasksStore = defineStore('tasks', () => {
  const tasks = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  async function loadTasks() {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.get('/tasks')

      tasks.value = response.data.map(task => ({
        ...task,
        id: String(task.id),
      }))
    } catch (e) {
      error.value = 'Aufgaben konnten nicht geladen werden'
      console.error(e)
    } finally {
      isLoading.value = false
    }
  }

  async function addTask(task) {
    error.value = null

    try {
      const response = await api.post('/tasks', task)

      tasks.value.unshift({
        ...response.data,
        id: String(response.data.id),
      })
    } catch (e) {
      error.value = 'Aufgabe konnte nicht gespeichert werden'
      console.error(e)
    }
  }

  async function toggleStatus(id) {
    error.value = null

    try {
      const response = await api.patch(`/tasks/${id}/toggle`)

      const index = tasks.value.findIndex(task => task.id === id)

      if (index !== -1) {
        tasks.value[index] = {
          ...response.data,
          id: String(response.data.id),
        }
      }
    } catch (e) {
      error.value = 'Status konnte nicht geändert werden'
      console.error(e)
    }
  }

  return {
    tasks,
    isLoading,
    error,
    loadTasks,
    addTask,
    toggleStatus,
  }
})