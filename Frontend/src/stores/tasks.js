import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '@/lib/api'

export const useTasksStore = defineStore('tasks', () => {
  const tasks = ref([])
  const isLoading = ref(false)
  const error = ref(null)
  const overdueTasks = ref([])

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
  async function loadOverdueTasks() {
  try {
    const response = await api.get('/tasks/overdue')

    overdueTasks.value = response.data
  } catch (e) {
    console.error(e)
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
  //Tasks update
  async function updateTask(id, task) {
    const response = await api.patch(`/tasks/${id}`, task)

    const index = tasks.value.findIndex(item => item.id === id)

    if (index !== -1) {
      tasks.value[index] = response.data
    }

    return response.data
  }

  async function deleteTask(id) {
    await api.delete(`/tasks/${id}`)

    tasks.value = tasks.value.filter(task => task.id !== id)
  }

  return {
    tasks,
    isLoading,
    error,
    loadTasks,
    addTask,
    toggleStatus,
    updateTask,
    deleteTask,
    overdueTasks,
    loadOverdueTasks,
  }
})