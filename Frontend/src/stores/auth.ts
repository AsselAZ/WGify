import { defineStore } from 'pinia'
import { ref } from 'vue'
import { members, type Member } from '@/lib/mockData'

export const useAuthStore = defineStore('auth', () => {
  const currentUser = ref<Member | null>(null)
  const registeredUsers = ref<Member[]>([])

  function loadUser() {
    const savedUser = localStorage.getItem('currentUser')
    const savedRegisteredUsers = localStorage.getItem('registeredUsers')

    if (savedUser) {
      currentUser.value = JSON.parse(savedUser)
    }

    if (savedRegisteredUsers) {
      registeredUsers.value = JSON.parse(savedRegisteredUsers)
    }
  }

  function login(email: string) {
    loadUser()

    const allUsers = [...members, ...registeredUsers.value]
    const user = allUsers.find(member => member.email === email)

    if (user) {
      currentUser.value = user
      localStorage.setItem('currentUser', JSON.stringify(user))
      return true
    }

    return false
  }

  function register(name: string, email: string) {
    const newUser: Member = {
      id: Date.now().toString(),
      name,
      email,
      role: 'mitglied',
      avatar: name.charAt(0).toUpperCase(),
    }

    registeredUsers.value.push(newUser)
    currentUser.value = newUser

    localStorage.setItem('registeredUsers', JSON.stringify(registeredUsers.value))
    localStorage.setItem('currentUser', JSON.stringify(newUser))

    return newUser
  }

  function logout() {
    currentUser.value = null
    localStorage.removeItem('currentUser')
  }

  return {
    currentUser,
    registeredUsers,
    loadUser,
    login,
    register,
    logout,
  }
})