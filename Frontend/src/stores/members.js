import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '@/lib/api'

export const useMembersStore = defineStore('members', () => {
  const members = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  async function loadMembers() {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.get('/members')
      members.value = response.data
    } catch (err) {
      error.value = 'Mitglieder konnten nicht geladen werden.'
      console.error(err)
    } finally {
      isLoading.value = false
    }
  }

  return {
    members,
    isLoading,
    error,
    loadMembers,
  }
})