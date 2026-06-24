import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '@/lib/api'

export const useAuthStore = defineStore('auth', () => {
  const currentUser = ref(null)

  function loadUser() {
    const savedUser = localStorage.getItem('currentUser')

    if (savedUser) {
      currentUser.value = JSON.parse(savedUser)
    }
  }

  async function register(name, email, password, passwordConfirmation) {
    const response = await api.post('/register', {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    })

    currentUser.value = response.data.user
    localStorage.setItem('currentUser', JSON.stringify(response.data.user))

    return response.data.user
  }

  async function login(email, password) {
    const response = await api.post('/login', {
      email,
      password,
    })

    currentUser.value = response.data.user
    localStorage.setItem('currentUser', JSON.stringify(response.data.user))

    return response.data.user
  }

  function logout() {
    currentUser.value = null
    localStorage.removeItem('currentUser')
  }

  return {
    currentUser,
    loadUser,
    register,
    login,
    logout,
  }
})