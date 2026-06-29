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

  async function register(data) {
    const response = await api.post('/register', {
      name: data.name,
      email: data.email,
      password: data.password,
      password_confirmation: data.passwordConfirmation,
      mode: data.mode,
      apartmentName: data.apartmentName,
      inviteCode: data.inviteCode,
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

  async function createApartment(apartmentName) {
    const response = await api.post('/apartments/create', {
      apartmentName,
    })

    currentUser.value = response.data.user
    localStorage.setItem('currentUser', JSON.stringify(response.data.user))

    return response.data
  }

  async function joinApartment(inviteCode) {
    const response = await api.post('/apartments/join', {
      inviteCode,
    })

    currentUser.value = response.data.user
    localStorage.setItem('currentUser', JSON.stringify(response.data.user))

    return response.data
  }

  async function leaveApartment() {
    const response = await api.post('/apartments/leave')

    currentUser.value = response.data.user
    localStorage.setItem('currentUser', JSON.stringify(response.data.user))

    return response.data
  }

  async function updateApartmentSettings(settings) {
    const response = await api.patch('/apartment/settings', settings)

    currentUser.value = response.data.user
    localStorage.setItem('currentUser', JSON.stringify(response.data.user))

    return response.data.user
  }

  async function updateAvatar(file) {
  const formData = new FormData()
  formData.append('avatar', file)

  const response = await api.post('/profile/avatar', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  })

  currentUser.value = response.data.user
  localStorage.setItem('currentUser', JSON.stringify(response.data.user))

  return response.data.user
}
async function deleteAvatar() {
  const response = await api.delete('/profile/avatar')

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
    createApartment,
    joinApartment,
    leaveApartment,
    updateApartmentSettings,
    updateAvatar,
    deleteAvatar,
  }
})