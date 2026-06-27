import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([])

  function addToast(type, title, message = '') {
    const id = `${Date.now()}-${Math.random()}`

    toasts.value.push({
      id,
      type,
      title,
      message,
    })

    setTimeout(() => {
      removeToast(id)
    }, 3500)
  }

  function success(title, message = '') {
    addToast('success', title, message)
  }

  function error(title, message = '') {
    addToast('error', title, message)
  }

  function info(title, message = '') {
    addToast('info', title, message)
  }

  function removeToast(id) {
    toasts.value = toasts.value.filter((toast) => toast.id !== id)
  }

  return {
    toasts,
    success,
    error,
    info,
    removeToast,
  }
})